<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stevenmaguire\Spotify\SpotifyWebAPI;
use Illuminate\Support\Facades\Redirect;

class SpotifyController extends Controller
{
    protected $spotify;

    public function __construct()
    {
        // Defina as credenciais do Spotify
        $this->spotify = new SpotifyWebAPI();
        $this->spotify->setClientId(env('SPOTIFY_CLIENT_ID'));
        $this->spotify->setClientSecret(env('SPOTIFY_CLIENT_SECRET'));
        $this->spotify->setRedirectUri(env('SPOTIFY_REDIRECT_URI'));
    }

    // Rota para redirecionar o usuário ao Spotify para login e autorização
    public function redirectToSpotify()
    {
        $scopes = [
            'user-read-playback-state', 'user-modify-playback-state', 'user-read-currently-playing'
        ];

        $authUrl = $this->spotify->getAuthorizeUrl([
            'scope' => implode(' ', $scopes)
        ]);

        return Redirect::to($authUrl);
    }

    // Rota para o callback após o login do Spotify
    public function handleCallback(Request $request)
    {
        if ($request->has('code')) {
            // O Spotify redirecionou com um código de autorização
            $code = $request->get('code');
            $accessToken = $this->spotify->requestAccessToken($code);

            // Salve o token de acesso para fazer chamadas posteriores
            session(['spotify_access_token' => $accessToken]);

            return redirect('/home');  // Redireciona após sucesso
        } else {
            return redirect('/login');
        }
    }

    // Função para obter informações da música atual
    public function getCurrentTrack()
    {
        $accessToken = session('spotify_access_token');

        if ($accessToken) {
            $this->spotify->setAccessToken($accessToken);

            $track = $this->spotify->getTrackCurrentPlayback();
            return response()->json($track);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // Função para pausar a música
    public function pauseMusic()
    {
        $accessToken = session('spotify_access_token');

        if ($accessToken) {
            $this->spotify->setAccessToken($accessToken);
            $this->spotify->pause();
            return response()->json(['message' => 'Music paused']);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // Função para reproduzir a música
    public function playMusic($trackUri)
    {
        $accessToken = session('spotify_access_token');

        if ($accessToken) {
            $this->spotify->setAccessToken($accessToken);
            $this->spotify->play(['uris' => [$trackUri]]);
            return response()->json(['message' => 'Playing music']);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }
}
