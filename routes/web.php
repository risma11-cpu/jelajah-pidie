<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/destinasi', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinasi/{destination}', [DestinationController::class, 'show'])
    ->whereNumber('destination')
    ->name('destinations.show');

// Tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');
});

// Pengguna login
Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

    Route::get('/favorit', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorit/{destination}', [FavoriteController::class, 'toggle'])
        ->whereNumber('destination')
        ->name('favorites.toggle');

    Route::post('/destinasi/{destination}/ulasan', [ReviewController::class, 'store'])
        ->whereNumber('destination')
        ->name('reviews.store');
    Route::delete('/ulasan/{review}', [ReviewController::class, 'destroy'])
        ->whereNumber('review')
        ->name('reviews.destroy');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin (login + role admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('destinasi', AdminDestinationController::class)
        ->except('show')
        ->parameters(['destinasi' => 'destination'])
        ->names('destinations');

    Route::resource('kategori', AdminCategoryController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['kategori' => 'category'])
        ->names('categories');

    Route::get('/ulasan', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/ulasan/{review}', [AdminReviewController::class, 'update'])
        ->whereNumber('review')
        ->name('reviews.update');
    Route::delete('/ulasan/{review}', [AdminReviewController::class, 'destroy'])
        ->whereNumber('review')
        ->name('reviews.destroy');
});