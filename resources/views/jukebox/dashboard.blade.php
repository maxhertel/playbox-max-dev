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

</body>
</html>
