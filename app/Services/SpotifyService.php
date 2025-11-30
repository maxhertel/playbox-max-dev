<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpotifyService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected string $baseUrl = 'https://api.spotify.com/v1';

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
        $this->redirectUri = config('services.spotify.redirect');
    }

    private function getToken()
    {
        return SpotifyToken::first()->access_token;
    }

    public function getAuthUrl()
    {
        $scopes = implode(' ', [
            'user-read-playback-state',
            'user-modify-playback-state',
            'user-read-currently-playing'
        ]);

        return "https://accounts.spotify.com/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'scope' => $scopes,
            'redirect_uri' => $this->redirectUri
        ]);
    }

    public function getAccessToken($code)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post('https://accounts.spotify.com/api/token', [
                'grant_type'   => 'authorization_code',
                'code'         => $code,
                'redirect_uri' => $this->redirectUri
            ]);

        if (!$response->successful()) {
            logger()->error('Spotify Token Error', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return null;
        }

        return $response->json();
    }


    public function searchTrack(string $query, string $token)
    {
        return Http::withToken($token)
            ->get($this->baseUrl . '/search', [
                'q' => $query,
                'type' => 'track',
                'limit' => 10
            ])->json();
    }

    public function getDevices($token)
    {
        return Http::withToken($token)
            ->get($this->baseUrl . '/me/player/devices')
            ->json();
    }
public function playTrack(string $token, string $trackUri): bool
{
    $response = Http::withToken($token)
        ->put('https://api.spotify.com/v1/me/player/play', [
            'uris' => [$trackUri]
        ]);

    return $response->successful();
}
    public function pause(string $token)
    {
        return Http::withToken($token)
            ->put($this->baseUrl . '/me/player/pause');
    }

    public function next(string $token)
    {
        return Http::withToken($token)
            ->post($this->baseUrl . '/me/player/next');
    }

    public function getCurrentlyPlaying($token)
    {
        $response = Http::withToken($token)
            ->get('https://api.spotify.com/v1/me/player/currently-playing');

        if ($response->status() == 204) {
            // Nada tocando
            return null;
        }

        return $response->json();
    }
}
