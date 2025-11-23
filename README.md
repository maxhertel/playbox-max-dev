composer create-project laravel/laravel playbox
Client ID
73170903781e4772a747de9fc7274e50

Client secret
be0a23114dc148dba15e3dd6ada91d7e


ngrok                                                                                                   (Ctrl+C to quit)                                                                                                                        ⚠️ Free Users: Agents ≤3.18.x stop connecting 12/17/25. Update or upgrade: https://ngrok.com/pricing                                                                                                                                            Session Status                online                                                                                    Account                       barplayzone@gmail.com (Plan: Free)                                                        Version                       3.33.0                                                                                    Region                        South America (sa)                                                                        Latency                       15ms                                                                                      Web Interface                 http://127.0.0.1:4040                                                                     Forwarding                    https://endoperidial-courdinative-lesley.ngrok-free.dev -> http://localhost:80                                                                                                                                    Connections                   ttl     opn     rt1     rt5     p50     p90                                                                             0       0       0.00    0.00    0.00    0.00                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               

Sim! Dá para fazer tudo diretamente usando HTTP (ou cURL) sem nenhuma biblioteca externa.
Na verdade, essa é até a forma mais estável, porque você controla toda a comunicação com a API do Spotify.

Abaixo eu te passo:

✔️ Como gerar tokens pelo OAuth

✔️ Como salvar o refresh token permanentemente

✔️ Como renovar o access token automaticamente

✔️ Como controlar o Spotify usando apenas requisições HTTP / cURL

✔️ Prontos para usar no Laravel

✅ 1) Fluxo correto: usar uma única conta e HTTP puro

Você só faz login no Spotify uma vez, com sua conta (via OAuth).
Depois salva no banco:

refresh_token

access_token

expires_at

Quando qualquer usuário acessar seu site → você usa sempre esse mesmo token.

🔐 2) Passo único: rota para login no Spotify (OAuth)
Rota de login:
Route::get('/spotify/login', function () {
    $query = http_build_query([
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'response_type' => 'code',
        'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
        'scope' => 'user-modify-playback-state user-read-playback-state user-read-currently-playing',
    ]);

    return redirect("https://accounts.spotify.com/authorize?$query");
});

🔄 3) Callback para obter access_token + refresh_token

Este controller salva o refresh token no banco.

SpotifyController.php
public function callback(Request $request)
{
    $code = $request->get('code');

    $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
    ]);

    $data = $response->json();

    SpotifyToken::updateOrCreate(
        ['id' => 1],
        [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at'   => now()->addSeconds($data['expires_in']),
        ]
    );

    return "Spotify conectado com sucesso!";
}

🔧 4) Serviço para renovar token automaticamente (sem libs)
app/Services/SpotifyService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SpotifyToken;

class SpotifyService
{
    public static function token()
    {
        $token = SpotifyToken::find(1);

        if (!$token) {
            throw new \Exception("Spotify não configurado.");
        }

        // Se o token expirou → renova
        if (now()->greaterThan($token->expires_at)) {

            $r = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token,
                'client_id' => env('SPOTIFY_CLIENT_ID'),
                'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
            ]);

            $data = $r->json();

            $token->access_token = $data['access_token'];
            $token->expires_at = now()->addSeconds($data['expires_in']);
            $token->save();
        }

        return $token->access_token;
    }
}

🎵 5) Controlar o Spotify com HTTP puro (sem libs!)
▶ Tocar música
public function play(Request $request)
{
    $uri = $request->uri;
    $token = SpotifyService::token();

    $response = Http::withHeaders([
        'Authorization' => "Bearer $token"
    ])->put("https://api.spotify.com/v1/me/player/play", [
        'uris' => [$uri]
    ]);

    return $response->json();
}

⏸️ Pausar
public function pause()
{
    $token = SpotifyService::token();

    Http::withHeaders([
        'Authorization' => "Bearer $token"
    ])->put("https://api.spotify.com/v1/me/player/pause");

    return "Pausado";
}

⏭️ Pular faixa
public function next()
{
    $token = SpotifyService::token();

    Http::withHeaders([
        'Authorization' => "Bearer $token"
    ])->post("https://api.spotify.com/v1/me/player/next");

    return "Next";
}

➕ Adicionar música à fila
public function queue(Request $request)
{
    $uri = $request->uri;
    $token = SpotifyService::token();

    Http::withHeaders([
        'Authorization' => "Bearer $token"
    ])->post("https://api.spotify.com/v1/me/player/queue?uri=$uri");

    return "Adicionada à fila!";
}

🔍 Buscar músicas
public function search(Request $request)
{
    $query = urlencode($request->q);
    $token = SpotifyService::token();

    $response = Http::withHeaders([
        'Authorization' => "Bearer $token"
    ])->get("https://api.spotify.com/v1/search?q=$query&type=track&limit=10");

    return $response->json();
}

🔥 6) Tudo sem bibliotecas — só HTTP request nativo do Laravel

Funciona em:

Laravel 8

Laravel 9

Laravel 10

Laravel 11

🏆 Resultado final

✔️ Seu site controla o Spotify
✔️ Apenas uma conta faz login
✔️ Todos os outros usuários podem:

Buscar músicas

Adicionar à fila

Pular

Tocar

Pausar

✔️ Sem pedir login para ninguém
✔️ Sem bibliotecas externas                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        