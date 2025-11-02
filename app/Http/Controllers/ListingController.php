<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ListingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Αρχική σελίδα: τελευταία listings (προβολή με primaryImage)
     */
    public function home(Request $request)
    {
        $latest = Listing::with('primaryImage')
            ->orderBy('published_at', 'desc')
            ->take(10)
            ->get();

        return view('home', ['latest' => $latest]);
    }

    /**
     * Λίστα αγγελιών με φίλτρα
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'q',
            'brand',
            'model',
            'os',
            'condition',
            'city',
            'min_price',
            'max_price',
            'year',
            'seller'
        ]);

        $listings = Listing::with('primaryImage')
            ->active()
            ->filters($filters)
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('listings.index', compact('listings', 'filters'));
    }

    /**
     * Προβολή αγγελίας (ορατή μόνο αν είναι active ή ανήκει στον χρήστη)
     */
    public function show(Listing $listing)
    {
        abort_unless(
            $listing->status === 'active' || optional(auth()->user())->id === $listing->user_id,
            404
        );

        $listing->load(['images', 'user']);

        $sellerActiveCount = Listing::active()
            ->where('user_id', $listing->user_id)
            ->count();

        return view('listings.show', [
            'listing' => $listing,
            'sellerActiveCount' => $sellerActiveCount,
        ]);
    }

    /**
     * Φόρμα δημιουργίας
     */
    public function create()
    {
        return view('listings.create');
    }

    /**
     * Αποθήκευση νέας αγγελίας
     */
    public function store(StoreListingRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['published_at'] = now();
        $data['status'] = $data['status'] ?? 'active';

        $listing = null;

        DB::transaction(function () use (&$listing, $data, $request) {
            $listing = Listing::create($data);
            $this->storeImages($request, $listing);
        });

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', 'Listing posted!');
    }

    /**
     * Φόρμα επεξεργασίας
     */
    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);
        $listing->load('images');
        return view('listings.edit', compact('listing'));
    }

    /**
     * Ενημέρωση αγγελίας
     * - Διαγραφή επιλεγμένων εικόνων
     * - Επανoρισμός primary εάν λείπει
     * - Προσθήκη νέων εικόνων (με σωστό ordering)
     */
    public function update(UpdateListingRequest $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        // ενημέρωση πεδίων
        $listing->update($request->validated());

        DB::transaction(function () use ($request, $listing) {

            //Per-image delete μέσω hidden inputs delete_images[]
            $ids = collect($request->input('delete_images', []))
                ->filter(fn($v) => is_numeric($v))
                ->map(fn($v) => (int) $v)
                ->unique()
                ->values();

            if ($ids->isNotEmpty()) {
                $toDelete = $listing->images()
                    ->whereIn('id', $ids)
                    ->get(); // μόνο δικές του εικόνες

                foreach ($toDelete as $img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }

            // Αν δεν υπάρχει πια primary μετά τις διαγραφές, όρισε μία
            if (!$listing->primaryImage()->exists()) {
                $first = $listing->images()->orderBy('ordering')->first();
                if ($first) {
                    $first->is_primary = true;
                    $first->save();
                }
            }

            // Upload νέων (προσθήκη στο τέλος με σωστό ordering)
            $this->storeImages($request, $listing);
        });

        $listing->refresh();

        return redirect()
            ->route('listings.show', ['listing' => $listing->id])
            ->with('success', 'Listing updated.');
    }

    /**
     * Διαγραφή αγγελίας
     */
    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);

        foreach ($listing->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $listing->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Listing deleted.');
    }

    /**
     * Αποθήκευση εικόνων
     * - Κολλάει τις νέες εικόνες στο τέλος (ordering)
     * - Ορίζει primary την πρώτη που ανεβαίνει, μόνο αν δεν υπάρχει ήδη primary
     */
    private function storeImages(Request $request, Listing $listing): void
    {
        if (!$request->hasFile('photos'))
            return;

        $incoming = $request->file('photos');
        if (!is_array($incoming))
            $incoming = [$incoming];

        // από ποιο ordering ξεκινάμε
        $startOrder = (int) $listing->images()->max('ordering');
        $startOrder = $startOrder >= 0 ? $startOrder + 1 : 0;

        $hasPrimary = $listing->primaryImage()->exists();

        foreach ($incoming as $i => $file) {
            if (!$file)
                continue;

            $path = $file->store("listings/{$listing->id}", 'public');

            $listing->images()->create([
                'path' => $path,
                'ordering' => $startOrder + $i,
                'is_primary' => (!$hasPrimary && $i === 0), // κάνε την 1η primary μόνο αν δεν υπάρχει ήδη
            ]);
        }
    }
}
