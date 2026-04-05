<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Crypto Asset Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen p-8">

    <h1 class="text-3xl font-bold text-center mb-8">
        💰 Crypto Asset Tracker
    </h1>

    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($coins as $coin)
        <div class="bg-gray-800 rounded-2xl p-6 shadow-lg">
            <div class="flex items-center gap-4 mb-4">
                <img src="{{ $coin['image'] }}" class="w-12 h-12" alt="{{ $coin['name'] }}">
                <div>
                    <h2 class="text-xl font-bold">{{ $coin['name'] }}</h2>
                    <span class="text-gray-400 uppercase text-sm">{{ $coin['symbol'] }}</span>
                </div>
            </div>

            <p class="text-2xl font-semibold">
                ${{ number_format($coin['current_price'], 2) }}
            </p>

            <p class="mt-2 text-sm font-medium
                {{ $coin['price_change_percentage_24h'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                {{ $coin['price_change_percentage_24h'] >= 0 ? '▲' : '▼' }}
                {{ number_format($coin['price_change_percentage_24h'], 2) }}% (24h)
            </p>

            <p class="mt-1 text-gray-400 text-sm">
                Volume: ${{ number_format($coin['total_volume']) }}
            </p>
        </div>
        @endforeach
    </div>

    <p class="text-center text-gray-500 text-xs mt-10">
        Data dari CoinGecko API · Refresh halaman untuk update terbaru
    </p>

</body>
</html>
