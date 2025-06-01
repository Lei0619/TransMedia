<?php

use Illuminate\Support\Facades\Route;
// use Inertia\Inertia; // Only uncomment if you are actively using Inertia.js
use App\Http\Controllers\ConversionController;


// --- PUBLIC ROUTES (No Authentication Required) ---

// Home Page (handled by ConversionController index, assuming it renders a Blade view)
// This replaces your duplicate root route and the Inertia Welcome page if not using Inertia.
Route::get('/', function () {
    return view('landing_page'); // Main landing page
});

Route::get('/conversions', [ConversionController::class, 'index'])->name('conversions.index');
Route::post('/conversions', [ConversionController::class, 'store'])->name('conversions.store');
Route::get('/conversions/{conversion}', [ConversionController::class, 'show'])->name('conversions.show');
Route::get('/conversions/{conversion}/download', [ConversionController::class, 'download'])->name('conversions.download');
Route::delete('/conversions/{conversion}', [ConversionController::class, 'destroy'])->name('conversions.destroy');

// External links or pages
Route::get('/facebook', function () {
    return view('facebook'); // Facebook-related page
})->name('facebook');

Route::get('/youtube', function () {
    return view('youtube'); // YouTube-related page
})->name('youtube');

Route::get('/tiktok', function () {
    return view('tiktok'); // TikTok-related page
})->name('tiktok');

// --- AUTHENTICATION ROUTES (Provided by Laravel Breeze/Jetstream) ---
// This line correctly includes the default authentication routes (login, register, forgot password, etc.)
require __DIR__.'/auth.php';


// --- AUTHENTICATED & VERIFIED ROUTES (User must be logged in and email verified) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    // This replaces your duplicate dashboard route.

    // Profile Routes (consolidated)

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
