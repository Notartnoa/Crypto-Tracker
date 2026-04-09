<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Asset Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-financial@0.1.1/dist/chartjs-chart-financial.js"></script>
</head>

<body class="bg-gray-950 text-white min-h-screen flex flex-col">

    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
            <span class="text-2xl">💰</span>
            <span class="text-xl font-bold">Crypto Tracker</span>
        </div>
        <div class="relative w-96">
            <input type="text" id="searchInput"
                placeholder="Cari koin... (contoh: bitcoin, solana)"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
            <div id="searchResults"
                class="absolute top-full mt-1 w-full bg-gray-800 border border-gray-700 rounded-lg z-50 hidden overflow-hidden">
            </div>
        </div>
    </nav>

    <div class="flex flex-1 overflow-hidden">

        <div id="coinListPanel" class="w-full flex flex-col overflow-hidden transition-all duration-300 border-r border-gray-800">

            <div class="flex gap-2 px-4 py-3 border-b border-gray-800 text-sm shrink-0">
                <span class="text-gray-400 self-center">Sort by:</span>
                <button onclick="sortCoins('market_cap')"
                    class="sort-btn px-3 py-1 rounded bg-blue-600 text-white" data-sort="market_cap">Market Cap</button>
                <button onclick="sortCoins('price')"
                    class="sort-btn px-3 py-1 rounded bg-gray-700 text-gray-300" data-sort="price">Price</button>
                <button onclick="sortCoins('change')"
                    class="sort-btn px-3 py-1 rounded bg-gray-700 text-gray-300" data-sort="change">24h Change</button>
            </div>

            <div class="grid grid-cols-4 px-4 py-2 text-xs text-gray-500 border-b border-gray-800 shrink-0">
                <span>#  Nama</span>
                <span class="text-right">Harga</span>
                <span class="text-right">24h %</span>
                <span class="text-right">Volume</span>
            </div>

            <div id="coinList" class="overflow-y-auto flex-1">
                @forelse ($coins as $index => $coin)
                <div class="coin-row grid grid-cols-4 px-4 py-3 border-b border-gray-800 hover:bg-gray-800 cursor-pointer transition"
                    onclick="loadChart('{{ $coin['id'] }}', '{{ $coin['name'] }}', '{{ $coin['symbol'] }}')"
                    data-price="{{ $coin['current_price'] ?? 0 }}"
                    data-change="{{ $coin['price_change_percentage_24h'] ?? 0 }}"
                    data-cap="{{ $coin['market_cap'] ?? 0 }}">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500 text-xs w-5">{{ $index + 1 }}</span>
                        <img src="{{ $coin['image'] }}" class="w-7 h-7 rounded-full" alt="{{ $coin['name'] }}"
                             onerror="this.src='https://via.placeholder.com/28'">
                        <div>
                            <p class="text-sm font-semibold">{{ $coin['name'] }}</p>
                            <p class="text-xs text-gray-500 uppercase">{{ $coin['symbol'] }}</p>
                        </div>
                    </div>
                    <div class="text-right text-sm self-center">
                        ${{ number_format($coin['current_price'] ?? 0, 2) }}
                    </div>
                    <div class="text-right text-sm self-center font-medium {{ ($coin['price_change_percentage_24h'] ?? 0) >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ ($coin['price_change_percentage_24h'] ?? 0) >= 0 ? '▲' : '▼' }}
                        {{ number_format(abs($coin['price_change_percentage_24h'] ?? 0), 2) }}%
                    </div>
                    <div class="text-right text-xs text-gray-400 self-center">
                        ${{ number_format(($coin['total_volume'] ?? 0) / 1e6, 1) }}M
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center h-64 text-gray-500 gap-3">
                    <span class="text-5xl">📡</span>
                    <p class="text-sm font-medium">Gagal memuat data koin</p>
                    <p class="text-xs text-gray-600">CoinGecko API mungkin sedang rate limit</p>
                    <button onclick="location.reload()"
                        class="mt-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
                        🔄 Coba Lagi
                    </button>
                </div>
                @endforelse
            </div>
        </div>

        <div id="chartSidePanel" class="hidden w-1/2 p-6 flex-col overflow-hidden border-l border-gray-800">
            <div class="flex items-center gap-4 mb-4 shrink-0">
                <img id="chartCoinImg" src="" class="w-10 h-10 rounded-full">
                <div>
                    <h2 id="chartCoinName" class="text-xl font-bold"></h2>
                    <p id="chartCoinSymbol" class="text-gray-400 text-sm uppercase"></p>
                </div>
                <div class="ml-auto text-right flex items-start gap-4">
                    <div>
                        <p id="chartCoinPrice" class="text-2xl font-bold"></p>
                        <p id="chartCoinChange" class="text-sm font-medium"></p>
                    </div>
                    <button onclick="closeChart()" class="text-gray-500 hover:text-white text-xl mt-1 leading-none">✕</button>
                </div>
            </div>

            <div class="flex gap-2 mb-4 shrink-0">
                <button onclick="changeRange(1)"  class="range-btn px-3 py-1 rounded text-xs bg-gray-700 text-gray-300" data-range="1">1H</button>
                <button onclick="changeRange(7)"  class="range-btn px-3 py-1 rounded text-xs bg-blue-600 text-white"   data-range="7">7D</button>
                <button onclick="changeRange(14)" class="range-btn px-3 py-1 rounded text-xs bg-gray-700 text-gray-300" data-range="14">14D</button>
                <button onclick="changeRange(30)" class="range-btn px-3 py-1 rounded text-xs bg-gray-700 text-gray-300" data-range="30">30D</button>
            </div>

            <div class="bg-gray-900 rounded-xl p-4 flex-1 flex flex-col overflow-hidden">
                <p class="text-xs text-gray-500 mb-3 shrink-0">Candlestick Chart (OHLC)</p>
                <div id="chartLoading" class="hidden flex-1 flex items-center justify-center text-gray-500 text-sm">
                    Memuat chart...
                </div>
                <div id="chartError" class="hidden flex-1 flex flex-col items-center justify-center text-gray-500 gap-2">
                    <span class="text-3xl">⚠️</span>
                    <p class="text-sm">Gagal memuat chart</p>
                </div>
                <div class="flex-1 relative" id="chartWrapper">
                    <canvas id="priceChart" class="absolute inset-0 w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let chartInstance = null;
        let allCoins      = @json($coins);
        let currentCoinId = null;

        async function loadChart(id, name, symbol) {
            currentCoinId = id;

            const panel = document.getElementById('chartSidePanel');
            panel.classList.remove('hidden');
            panel.classList.add('flex');
            document.getElementById('coinListPanel').classList.remove('w-full');
            document.getElementById('coinListPanel').classList.add('w-1/2');

            const coin = allCoins.find(c => c.id === id);
            if (coin) {
                document.getElementById('chartCoinImg').src            = coin.image;
                document.getElementById('chartCoinName').textContent   = coin.name;
                document.getElementById('chartCoinSymbol').textContent = coin.symbol;
                document.getElementById('chartCoinPrice').textContent  = '$' + coin.current_price.toLocaleString();
                const change   = coin.price_change_percentage_24h;
                const changeEl = document.getElementById('chartCoinChange');
                changeEl.textContent = (change >= 0 ? '▲ ' : '▼ ') + Math.abs(change).toFixed(2) + '% (24h)';
                changeEl.className   = 'text-sm font-medium ' + (change >= 0 ? 'text-green-400' : 'text-red-400');
            }

            setActiveRange(7);
            await fetchAndRender(id, 7);
        }

        async function fetchAndRender(id, days) {
            document.getElementById('chartLoading').classList.remove('hidden');
            document.getElementById('chartLoading').classList.add('flex');
            document.getElementById('chartWrapper').classList.add('hidden');
            document.getElementById('chartError').classList.add('hidden');

            try {
                const res = await fetch(`/chart/${id}?days=${days}`);
                const ohlc = await res.json();

                if (!ohlc || !Array.isArray(ohlc) || ohlc.length === 0) {
                    throw new Error('Data kosong');
                }

                const candleData = ohlc.map(d => ({
                    x: d[0],
                    o: d[1],
                    h: d[2],
                    l: d[3],
                    c: d[4],
                }));

                if (chartInstance) chartInstance.destroy();

                document.getElementById('chartLoading').classList.add('hidden');
                document.getElementById('chartLoading').classList.remove('flex');
                document.getElementById('chartWrapper').classList.remove('hidden');

                const ctx = document.getElementById('priceChart').getContext('2d');

                chartInstance = new Chart(ctx, {
                    type: 'candlestick',
                    data: {
                        datasets: [{
                            label: currentCoinId,
                            data: candleData,
                            color: {
                                up:        '#34d399',
                                down:      '#f87171',
                                unchanged: '#9ca3af',
                            },
                            borderColor: {
                                up:        '#34d399',
                                down:      '#f87171',
                                unchanged: '#9ca3af',
                            },
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                type: 'timeseries',
                                time: { unit: days <= 1 ? 'hour' : 'day' },
                                ticks: { color: '#6b7280', maxTicksLimit: 10 },
                                grid:  { color: '#1f2937' },
                            },
                            y: {
                                ticks: {
                                    color: '#6b7280',
                                    callback: v => '$' + v.toLocaleString()
                                },
                                grid: { color: '#1f2937' },
                            }
                        }
                    }
                });

            } catch (e) {
                document.getElementById('chartLoading').classList.add('hidden');
                document.getElementById('chartLoading').classList.remove('flex');
                document.getElementById('chartError').classList.remove('hidden');
                document.getElementById('chartError').classList.add('flex');
            }
        }

        function changeRange(days) {
            setActiveRange(days);
            fetchAndRender(currentCoinId, days);
        }

        function setActiveRange(days) {
            document.querySelectorAll('.range-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-700', 'text-gray-300');
            });
            const active = document.querySelector(`[data-range="${days}"]`);
            if (active) {
                active.classList.add('bg-blue-600', 'text-white');
                active.classList.remove('bg-gray-700', 'text-gray-300');
            }
        }

        function closeChart() {
            const panel = document.getElementById('chartSidePanel');
            panel.classList.add('hidden');
            panel.classList.remove('flex');
            document.getElementById('coinListPanel').classList.add('w-full');
            document.getElementById('coinListPanel').classList.remove('w-1/2');
            if (chartInstance) { chartInstance.destroy(); chartInstance = null; }
            currentCoinId = null;
        }

        function sortCoins(type) {
            document.querySelectorAll('.sort-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-700', 'text-gray-300');
            });
            document.querySelector(`[data-sort="${type}"]`).classList.add('bg-blue-600', 'text-white');
            document.querySelector(`[data-sort="${type}"]`).classList.remove('bg-gray-700', 'text-gray-300');

            const rows = Array.from(document.querySelectorAll('.coin-row'));
            rows.sort((a, b) => {
                if (type === 'price')  return parseFloat(b.dataset.price)  - parseFloat(a.dataset.price);
                if (type === 'change') return parseFloat(b.dataset.change) - parseFloat(a.dataset.change);
                return parseFloat(b.dataset.cap) - parseFloat(a.dataset.cap);
            });
            const list = document.getElementById('coinList');
            rows.forEach(r => list.appendChild(r));
        }

        // ── Search ───────────────────────────────────────────────
        const searchInput   = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        let searchTimer;

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            const q = searchInput.value.trim();
            if (q.length < 2) { searchResults.classList.add('hidden'); return; }

            searchTimer = setTimeout(async () => {
                try {
                    const res   = await fetch(`/search?q=${encodeURIComponent(q)}`);
                    const coins = await res.json();
                    if (!coins.length) { searchResults.classList.add('hidden'); return; }

                    searchResults.innerHTML = coins.map(c => `
                        <div class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 cursor-pointer text-sm"
                            onclick="loadChart('${c.id}', '${c.name}', '${c.symbol}'); searchResults.classList.add('hidden'); searchInput.value='';">
                            <img src="${c.thumb}" class="w-6 h-6 rounded-full" onerror="this.src='https://via.placeholder.com/24'">
                            <span class="font-medium">${c.name}</span>
                            <span class="text-gray-500 uppercase text-xs">${c.symbol}</span>
                        </div>
                    `).join('');
                    searchResults.classList.remove('hidden');
                } catch (e) {
                    searchResults.classList.add('hidden');
                }
            }, 400);
        });

        document.addEventListener('click', e => {
            if (!searchInput.contains(e.target)) searchResults.classList.add('hidden');
        });
    </script>

</body>
</html>
