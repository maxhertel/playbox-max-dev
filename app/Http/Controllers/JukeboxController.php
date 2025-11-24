<?php

namespace App\Http\Controllers;

use App\Models\JukeboxQueue;
use App\Services\SpotifyService;
use Illuminate\Http\Request;

class JukeboxController extends Controller
{
    public function index()
    {
        $nowPlaying = JukeboxQueue::with('user')
            ->where('is_playing', true)
            ->first();

        $queue = JukeboxQueue::with('user')
            ->where('is_playing', false)
            ->orderBy('id')
            ->limit(10)
            ->get();

        return view('jukebox.dashboard', compact('nowPlaying', 'queue'));
    }

    public function status()
    {
        $nowPlaying = JukeboxQueue::with('user')
            ->where('is_playing', true)
            ->first();

        $queue = JukeboxQueue::with('user')
            ->where('is_playing', false)
            ->orderBy('id')
            ->limit(10)
            ->get();

        return response()->json([
            'now_playing' => $nowPlaying,
            'queue' => $queue
        ]);
    }

    public function search(Request $request, SpotifyService $spotify)
{
    $token = session('spotify_token');

    if (!$token) {
        return response()->json(['error' => 'Spotify não autenticado'], 401);
    }

    return $spotify->searchTrack($request->q, $token);
}

public function addToQueue(Request $request)
{
    JukeboxQueue::create([
        'user_id'    => auth()->id(),
        'track_name' => $request->track_name,
        'track_uri'  => $request->track_uri
    ]);

    return response()->json(['success' => true]);
}

}
