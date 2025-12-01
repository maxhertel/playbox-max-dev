<?php

namespace App\Http\Controllers;

use App\Models\SpotifyToken;
use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SpotifyController extends Controller
{
    public function auth(SpotifyService $spotify)
    {
        $spotify =  new SpotifyService();

        return redirect($spotify->getAuthUrl());
    }

    public function callback(Request $request, SpotifyService $spotify)
    {
        if (!$request->has('code')) {
            return response()->json(['error' => 'Código não recebido'], 400);
        }

        $token = $spotify->getAccessToken($request->code);

        SpotifyToken::updateOrCreate(
    ['id' => 1],
    [
        'access_token' => $token['access_token'],
        'refresh_token' => $token['refresh_token'],
        'expires_at' => now()->addSeconds($token['expires_in'])
    ]
);


        return redirect('/jukebox');
    }


    public function search(Request $request, SpotifyService $spotify)
    {
        $token = session('spotify_token');

        return $spotify->searchTrack($request->q, $token);
    }

    public function play(Request $request, SpotifyService $spotify)
    {
        $token = session('spotify_token');

        return $spotify->playTrack(
            $token,
            $request->track_uri,
            $request->device_id
        );
    }
}
