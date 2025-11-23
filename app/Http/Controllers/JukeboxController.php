<?php

namespace App\Http\Controllers;

use App\Models\JukeboxQueue;

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
}
