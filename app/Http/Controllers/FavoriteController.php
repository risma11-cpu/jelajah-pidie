<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $destinations = $request->user()
            ->favorites()
            ->with('category')
            ->withApprovedRatings()
            ->orderBy('name')
            ->paginate(9);

        return view('favorites.index', ['destinations' => $destinations]);
    }

    public function toggle(Request $request, Destination $destination): RedirectResponse
    {
        $result = $request->user()->favorites()->toggle($destination->id);

        $message = count($result['attached']) > 0
            ? 'Destinasi ditambahkan ke favorit.'
            : 'Destinasi dihapus dari favorit.';

        return back()->with('success', $message);
    }
}