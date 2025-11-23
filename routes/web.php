<?php

use App\Http\Controllers\JukeboxController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpotifyController;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/jukebox', [JukeboxController::class, 'index']);
Route::get('/jukebox/status', [JukeboxController::class, 'status']);

Route::get('/admin/artisan/run', function () {

  

    // Comandos que você quer rodar
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');

    // dump-autoload NÃO é Artisan, é Composer — então não entra aqui

    return response()->json([
        'status' => 'ok',
        'message' => 'Comandos executados com sucesso',
        'output' => Artisan::output()
    ]);

});

require __DIR__.'/auth.php';
