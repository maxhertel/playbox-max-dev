<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SpotifyController extends Controller
{
    public function auth(SpotifyService $spotify)
    {
        return redirect($spotify->getAuthUrl());
    }

    public function callback(Request $request, SpotifyService $spotify)
    {
        $token = $spotify->getAccessToken($request->code);

        session([
            'spotify_token' => $token['access_token']
        ]);

        return redirect('/spotify/painel');
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
