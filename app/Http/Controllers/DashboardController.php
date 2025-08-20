<?php

namespace App\Http\Controllers;

use App\Models\Listing;

class DashboardController extends Controller
{
    public function index()
    {
        $listings = Listing::with('primaryImage')
            ->where('user_id', auth()->id())
            ->latest('published_at')
            ->paginate(10);

        return view('dashboard.index', compact('listings'));
    }
}
