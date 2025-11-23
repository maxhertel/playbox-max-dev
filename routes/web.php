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


Route::get('/spotify/auth', [SpotifyController::class, 'auth']);
Route::get('/spotify/callback', [SpotifyController::class, 'callback']);

Route::get('/spotify/search', [SpotifyController::class, 'search']);
Route::post('/spotify/play', [SpotifyController::class, 'play']);   



require __DIR__.'/auth.php';
