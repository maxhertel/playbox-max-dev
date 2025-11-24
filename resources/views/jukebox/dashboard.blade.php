<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Jukebox</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-10">

    <h1 class="text-3xl font-bold mb-8">🎵 Jukebox</h1>
<!-- Pesquisa -->
<div class="bg-gray-800 p-6 rounded-xl mb-8">
    <h2 class="text-xl mb-4">🔍 Buscar música</h2>

    <input
        type="text"
        id="search"
        placeholder="Digite nome da música..."
        class="w-full p-3 rounded bg-gray-700 text-white mb-4"
    >

    <div id="results" class="space-y-2"></div>
</div>
    <!-- Música atual -->
    <div class="bg-gray-800 p-6 rounded-xl mb-10">
        <h2 class="text-xl mb-2">🎧 Tocando agora</h2>
        <div id="now-playing">
            @if($nowPlaying)
                <p><strong>{{ $nowPlaying->track_name }}</strong></p>
                <p class="text-sm text-gray-400">
                    Pedido por: {{ $nowPlaying->user->name ?? 'Anônimo' }}
                </p>
            @else
                <p>Nenhuma música tocando</p>
            @endif
        </div>
    </div>
    <!-- Música atual -->
<div class="bg-gray-800 p-6 rounded-xl mb-10">
    <h2 class="text-xl mb-4">🎧 Tocando agora</h2>

    <div id="now-playing" class="flex items-center gap-6">
        <div id="cover" class="w-32 h-32 bg-gray-700 rounded-lg"></div>

        <div>
            <p id="track-name" class="text-lg font-bold">---</p>
            <p id="track-artist" class="text-gray-400">---</p>
            <p id="device" class="text-sm text-gray-500 mt-2">---</p>
        </div>
    </div>
</div>

    <!-- Próximas músicas -->
    <div class="bg-gray-800 p-6 rounded-xl">
        <h2 class="text-xl mb-4">⏭ Próximas na fila</h2>
        <ul id="queue-list" class="space-y-2">
            @foreach($queue as $item)
                <li class="bg-gray-700 p-3 rounded-lg">
                    🎵 {{ $item->track_name }}
                    <span class="text-gray-400 text-sm block">
                        Pedido por: {{ $item->user->name ?? 'Anônimo' }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>

<script>
    async function updateJukebox() {
        const res = await fetch('/jukebox/status');
        const data = await res.json();

        // Atualiza música atual
        const now = document.getElementById('now-playing');
        if (data.now_playing) {
            now.innerHTML = `
                <p><strong>${data.now_playing.track_name}</strong></p>
                <p class="text-sm text-gray-400">
                    Pedido por: ${data.now_playing.user?.name ?? 'Anônimo'}
                </p>
            `;
        } else {
            now.innerHTML = '<p>Nenhuma música tocando</p>';
        }

        // Atualiza fila
        const list = document.getElementById('queue-list');
        list.innerHTML = '';

        data.queue.forEach(item => {
            list.innerHTML += `
                <li class="bg-gray-700 p-3 rounded-lg">
                    🎵 ${item.track_name}
                    <span class="text-gray-400 text-sm block">
                        Pedido por: ${item.user?.name ?? 'Anônimo'}
                    </span>
                </li>
            `;
        });
    }

    setInterval(updateJukebox, 5000);
</script>
<script>
const searchInput = document.getElementById('search');
const results = document.getElementById('results');

let debounceTimer = null;

searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(async () => {
        const q = searchInput.value;
        if (!q) return;

        const res = await fetch(`/jukebox/search?q=${encodeURIComponent(q)}`);
        const data = await res.json();

        results.innerHTML = '';

        const tracks = data.tracks?.items || [];

        tracks.forEach(track => {
            const div = document.createElement('div');
            div.className = 'bg-gray-700 p-3 rounded flex justify-between items-center';

            div.innerHTML = `
                <span>
                    🎵 <strong>${track.name}</strong> - ${track.artists[0].name}
                </span>
                <button class="bg-green-600 px-4 py-1 rounded">
                    Adicionar
                </button>
            `;

            div.querySelector('button').onclick = async () => {
                await fetch('/jukebox/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        track_name: `${track.name} - ${track.artists[0].name}`,
                        track_uri: track.uri
                    })
                });

                alert('Adicionado à fila!');
            };

            results.appendChild(div);
        });
    }, 500);
});
</script>
<script>
async function updateNowPlaying() {
    const res = await fetch('/spotify/current');
    const data = await res.json();

    const cover = document.getElementById('cover');
    const name = document.getElementById('track-name');
    const artist = document.getElementById('track-artist');
    const device = document.getElementById('device');

    if (!data.playing || !data.playing.item) {
        name.innerText = 'Nada tocando agora';
        artist.innerText = '';
        device.innerText = '';
        cover.innerHTML = '';
        return;
    }

    const track = data.playing.item;
    const image = track.album.images[0]?.url ?? '';

    cover.innerHTML = image
        ? `<img src="${image}" class="w-32 h-32 rounded-lg object-cover">`
        : '';

    name.innerText = track.name;
    artist.innerText = track.artists[0].name;
    device.innerText = `📱 Tocando em: ${data.playing.device?.name ?? 'Desconhecido'}`;
}

setInterval(updateNowPlaying, 5000);
updateNowPlaying();
</script>

</body>
</html>
