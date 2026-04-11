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
            const arrow    = change >= 0
                ? `<svg width="11" height="11" viewBox="0 0 24 24" fill="none"><path d="M12 19V5M5 12l7-7 7 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`
                : `<svg width="11" height="11" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M19 12l-7 7-7-7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
            changeEl.innerHTML = arrow + Math.abs(change).toFixed(2) + '% (24h)';
            changeEl.className = 'text-xs font-semibold flex items-center justify-end gap-1 ' + (change >= 0 ? 'text-emerald-400' : 'text-red-400');
        }

        setActiveRange(7);
        await fetchAndRender(id, 7);
    }

    async function fetchAndRender(id, days) {
        document.getElementById('chartLoading').classList.remove('hidden');
        document.getElementById('chartLoading').classList.add('flex');
        document.getElementById('chartWrapper').classList.add('hidden');
        document.getElementById('chartError').classList.add('hidden');
        document.getElementById('chartError').classList.remove('flex');

        try {
            const res  = await fetch(`/chart/${id}?days=${days}`);
            const ohlc = await res.json();

            if (!ohlc || !Array.isArray(ohlc) || ohlc.length === 0) throw new Error('Data kosong');

            const candleData = ohlc.map(d => ({ x: d[0], o: d[1], h: d[2], l: d[3], c: d[4] }));

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
                        color:       { up: '#34d399', down: '#f87171', unchanged: '#9ca3af' },
                        borderColor: { up: '#34d399', down: '#f87171', unchanged: '#9ca3af' },
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
                            ticks: { color: '#6b7280', maxTicksLimit: 10, font: { family: 'Poppins' } },
                            grid:  { color: '#1f2937' },
                        },
                        y: {
                            ticks: {
                                color: '#6b7280',
                                font: { family: 'Poppins' },
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
            btn.classList.add('bg-gray-800', 'text-gray-400');
        });
        const active = document.querySelector(`[data-range="${days}"]`);
        if (active) {
            active.classList.add('bg-blue-600', 'text-white');
            active.classList.remove('bg-gray-800', 'text-gray-400');
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
            btn.classList.add('bg-gray-800', 'text-gray-400');
        });
        document.querySelector(`[data-sort="${type}"]`).classList.add('bg-blue-600', 'text-white');
        document.querySelector(`[data-sort="${type}"]`).classList.remove('bg-gray-800', 'text-gray-400');

        const rows = Array.from(document.querySelectorAll('.coin-row'));
        rows.sort((a, b) => {
            if (type === 'price')  return parseFloat(b.dataset.price)  - parseFloat(a.dataset.price);
            if (type === 'change') return parseFloat(b.dataset.change) - parseFloat(a.dataset.change);
            return parseFloat(b.dataset.cap) - parseFloat(a.dataset.cap);
        });
        const list = document.getElementById('coinList');
        rows.forEach(r => list.appendChild(r));
    }
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
                    <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-700 cursor-pointer text-sm transition-colors"
                        onclick="loadChart('${c.id}', '${c.name}', '${c.symbol}'); searchResults.classList.add('hidden'); searchInput.value='';">
                        <img src="${c.thumb}" class="w-6 h-6 rounded-full" onerror="this.src='https://via.placeholder.com/24'">
                        <span class="font-medium">${c.name}</span>
                        <span class="text-gray-500 uppercase text-xs font-semibold ml-auto">${c.symbol}</span>
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
