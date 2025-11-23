<?php

namespace App\Http\Controllers;

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

    if (!$token || !isset($token['access_token'])) {
        return response()->json([
            'error' => 'Falha ao obter token do Spotify',
            'spotify_response' => $token
        ], 500);
    }

    session([
        'spotify_token' => $token['access_token']
    ]);

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
