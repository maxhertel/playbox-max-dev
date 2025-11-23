<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpotifyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/login/spotify', [SpotifyController::class, 'redirectToSpotify']);
Route::get('/spotify/callback', [SpotifyController::class, 'handleCallback']);
Route::get('/spotify/current-track', [SpotifyController::class, 'getCurrentTrack']);
Route::get('/spotify/pause', [SpotifyController::class, 'pauseMusic']);
Route::post('/spotify/play/{trackUri}', [SpotifyController::class, 'playMusic']);



require __DIR__.'/auth.php';
