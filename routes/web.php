<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
// use Inertia\Inertia; // Only uncomment if you are actively using Inertia.js
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\DashboardController;


// --- PUBLIC ROUTES (No Authentication Required) ---

// Home Page (handled by ConversionController index, assuming it renders a Blade view)
// This replaces your duplicate root route and the Inertia Welcome page if not using Inertia.
Route::get('/', [ConversionController::class, 'index'])->name('home');

// Landing Page (uses a direct Blade view)
Route::get('/landing', function () {
    return view('landing_page');
})->name('landing'); // Added a name for consistency

// Converter Page (uses a direct Blade view)
Route::get('/converter', function () {
    return view('converter');
})->name('converter'); // Added a name for consistency


// --- AUTHENTICATION ROUTES (Provided by Laravel Breeze/Jetstream) ---
// This line correctly includes the default authentication routes (login, register, forgot password, etc.)
require __DIR__.'/auth.php';


// --- AUTHENTICATED & VERIFIED ROUTES (User must be logged in and email verified) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    // This replaces your duplicate dashboard route.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes (consolidated)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Conversion Management (CRUD, excluding create/edit views as they might be handled differently or not needed)
    Route::resource('conversions', ConversionController::class)->except(['create', 'edit']);
    // Custom route for authenticated user's own conversions
    Route::get('/my-conversions', [ConversionController::class, 'myConversions'])->name('conversions.mine');
});

// --- DOWNLOAD ROUTE (Authentication and Download Tracking Middleware) ---
Route::get('/download/{conversion}', [ConversionController::class, 'download'])
    ->name('download')
    ->middleware(['auth', 'track.downloads']); // Assumes 'track.downloads' is registered in Kernel.php

// --- BROADCASTING AUTHENTICATION (For Laravel Echo/WebSockets) ---
// This is typically for authenticating private/presence channels for real-time features.
Route::middleware(['auth'])->group(function () {
    Route::post('/broadcasting/auth', function () {
        return request()->user();
    });
});