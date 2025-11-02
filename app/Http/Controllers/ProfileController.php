<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Listing;

class ProfileController extends Controller
{
    public function public(User $user)
    {
        $activeCount = Listing::active()->where('user_id', $user->id)->count();


        $listings = Listing::with('primaryImage')
            ->active()
            ->where('user_id', $user->id)
            ->latest('published_at')
            ->paginate(12);

        return view('user.show', compact('user', 'activeCount', 'listings'));
    }

    /**
     * Προβολή σελίδας προφίλ.
     */
    public function show()
    {
        $user = Auth::user();
        $listings = $user->listings()->latest()->get();

        return view('profile.show', compact('user', 'listings'));
    }

    /**
     * Προβολή φόρμας επεξεργασίας.
     */
    public function edit(): \Illuminate\View\View
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Ενημέρωση στοιχείων χρήστη.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        // Ανέβασμα νέου avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->city = $request->city;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Διαγραφή λογαριασμού χρήστη.
     */
    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
            Storage::delete('public/' . $user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'The account was successfully deleted.');
    }
}
