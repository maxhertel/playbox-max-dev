Perfeito — então vamos consertar o que você já começou e transformar no modelo certo de CONTA CENTRAL (jukebox real) ✅

Você já estava perto, só misturou dois fluxos diferentes sem perceber.

✅ O MODELO CERTO (o que você realmente quer)

Você precisa APENAS disso:

1 ÚNICA conta Spotify (do bar)
1 ÚNICO login OAuth (feito por você)

Usuários do site:
❌ NÃO fazem login no Spotify
✅ Só escolhem músicas

Quem controla o player:
➡️ SOMENTE o Laravel com o token da conta do bar

O erro que estava te travando

Você fez isso sem perceber:

❌ misturou:

login do dono do bar (você)

com login do público (clientes)

E o Spotify passou a pedir autorização para cada pessoa.

Como ajustar isso no seu projeto
1. Só você faz login no Spotify

Crie rota só para você:

Route::get('/admin/spotify/login', [SpotifyController::class, 'login']);
Route::get('/admin/spotify/callback', [SpotifyController::class, 'callback']);


E não mostre isso para usuários comuns.

2. Salve o token em banco (não em sessão)

Crie tabela:

php artisan make:migration create_spotify_tokens_table


No migration:

Schema::create('spotify_tokens', function (Blueprint $table) {
    $table->id();
    $table->text('access_token');
    $table->text('refresh_token');
    $table->timestamps();
});


Modelo:

class SpotifyToken extends Model {
    protected $fillable = ['access_token', 'refresh_token'];
}

3. No callback você salva o token fixo
public function callback(Request $request, SpotifyService $spotify)
{
    $token = $spotify->getAccessToken($request->code);

    SpotifyToken::updateOrCreate(
        ['id' => 1],
        [
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token']
        ]
    );

    return redirect('/admin/dashboard');
}

4. Sempre toque música com o token fixo

Nunca use session().

private function getToken()
{
    return SpotifyToken::first()->access_token;
}

5. Deixar o celular do bar sempre ativo

No celular do bar:

✅ Spotify aberto
✅ Conta do bar logada
✅ Volume fixo
✅ Não deixar entrar em economia de bateria

Agora seu sistema vira um Jukebox real

Usuários fazem:

Buscar música → escolher → entra na fila


Laravel faz:

Toca na conta do bar → sai na caixa de som

Agora a verdade importante

Mesmo com esse modelo, o Spotify tem limites:

Se ficar muito tempo sem atividade ele “dorme”

Algumas vezes precisa dar Play manual

Premium é obrigatório

Quer que eu te monte:

refresh automático do token?

detecção automática do celular (device_id)?

botão Play manual?

Me responde:

👉 quer que eu monte o modo “à prova de falha”?