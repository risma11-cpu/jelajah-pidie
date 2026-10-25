<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Review;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(RecommendationService $recommendations): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'destinasi' => Destination::count(),
                'kategori' => Category::count(),
                'pengguna' => User::count(),
                'ulasan' => Review::count(),
                'ulasan_menunggu' => Review::where('status', 'pending')->count(),
            ],
            'ml' => $recommendations->status(),
        ]);
    }
}