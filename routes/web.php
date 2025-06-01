<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/', [ConversionController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';

Route::get('/landing', function () {
    return view('landing_page');
});

Route::get('/converter', function () {
    return view('converter');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Conversion Management
    Route::resource('conversions', ConversionController::class)->except(['create', 'edit']);
    Route::get('/my-conversions', [ConversionController::class, 'myConversions'])->name('conversions.mine');
});

// Download Route (with middleware for tracking)
Route::get('/download/{conversion}', [ConversionController::class, 'download'])
    ->name('download')
    ->middleware(['auth', 'track.downloads']);

// Broadcasting Authentication
Route::middleware(['auth'])->group(function () {
    Route::post('/broadcasting/auth', function () {
        return auth()->user();
    });
});
