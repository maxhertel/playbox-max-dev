<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SpotifyToken;

class SpotifyService
{
    public static function getAccessToken()
    {
        $token = SpotifyToken::first();

        if (!$token) {
            throw new \Exception("Token do Spotify não configurado.");
        }

        // Verificar se token está expirado
        if (now()->greaterThan($token->expires_at)) {
            // Renovar token
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token,
                'client_id' => env('SPOTIFY_CLIENT_ID'),
                'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
            ]);

            $data = $response->json();

            $token->access_token = $data['access_token'];
            $token->expires_at = now()->addSeconds($data['expires_in']);
            $token->save();
        }

        return $token->access_token;
    }
}
