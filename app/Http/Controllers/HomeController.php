<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Destination::query()
            ->with(['category', 'coverPhoto'])
            ->withApprovedRatings()
            ->orderByRaw('avg_rating IS NULL')
            ->orderByDesc('avg_rating')
            ->orderBy('id')
            ->limit(6)
            ->get();

        $categories = Category::query()
            ->withCount('destinations')
            ->orderBy('name')
            ->get();

        return view('home', [
            'featured' => $featured,
            'categories' => $categories,
            'totalDestinations' => Destination::count(),
        ]);
    }
}