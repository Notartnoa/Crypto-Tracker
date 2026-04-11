<div id="coinListPanel" class="w-full flex flex-col overflow-hidden transition-all duration-300 border-r border-gray-800">
    <div class="flex gap-2 px-4 py-3 border-b border-gray-800 text-sm shrink-0 items-center">
        <span class="text-gray-400 text-xs font-medium">Sort by:</span>
        <button onclick="sortCoins('market_cap')"
            class="sort-btn px-3 py-1 rounded-full text-xs font-medium bg-blue-600 text-white" data-sort="market_cap">Market Cap</button>
        <button onclick="sortCoins('price')"
            class="sort-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-400 hover:bg-gray-700 transition" data-sort="price">Price</button>
        <button onclick="sortCoins('change')"
            class="sort-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-400 hover:bg-gray-700 transition" data-sort="change">24h Change</button>
    </div>
    <div class="grid grid-cols-4 px-4 py-2 text-xs text-gray-500 font-medium border-b border-gray-800 shrink-0">
        <span>#&nbsp;&nbsp;Nama</span>
        <span class="text-right">Harga</span>
        <span class="text-right">24h %</span>
        <span class="text-right">Volume</span>
    </div>
    <div id="coinList" class="overflow-y-auto flex-1">
        @forelse ($coins as $index => $coin)
        <div class="coin-row grid grid-cols-4 px-4 py-3 border-b border-gray-800/60 hover:bg-gray-800/50 cursor-pointer transition-colors"
            onclick="loadChart('{{ $coin['id'] }}', '{{ $coin['name'] }}', '{{ $coin['symbol'] }}')"
            data-price="{{ $coin['current_price'] ?? 0 }}"
            data-change="{{ $coin['price_change_percentage_24h'] ?? 0 }}"
            data-cap="{{ $coin['market_cap'] ?? 0 }}">
            <div class="flex items-center gap-3">
                <span class="text-gray-600 text-xs w-5 font-medium">{{ $index + 1 }}</span>
                <img src="{{ $coin['image'] }}" class="w-7 h-7 rounded-full" alt="{{ $coin['name'] }}"
                     onerror="this.src='https://via.placeholder.com/28'">
                <div>
                    <p class="text-sm font-semibold leading-tight">{{ $coin['name'] }}</p>
                    <p class="text-xs text-gray-500 uppercase font-medium">{{ $coin['symbol'] }}</p>
                </div>
            </div>
            <div class="text-right text-sm self-center font-medium">
                ${{ number_format($coin['current_price'] ?? 0, 2) }}
            </div>
            <div class="text-right text-sm self-center font-semibold flex items-center justify-end gap-1 {{ ($coin['price_change_percentage_24h'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                @if(($coin['price_change_percentage_24h'] ?? 0) >= 0)
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                    <path d="M12 19V5M5 12l7-7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @else
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5v14M19 12l-7 7-7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @endif
                {{ number_format(abs($coin['price_change_percentage_24h'] ?? 0), 2) }}%
            </div>
            <div class="text-right text-xs text-gray-400 self-center font-medium">
                ${{ number_format(($coin['total_volume'] ?? 0) / 1e6, 1) }}M
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center h-64 text-gray-500 gap-3">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.13 5.13A9.97 9.97 0 0 0 2 12c0 5.52 4.48 10 10 10a9.97 9.97 0 0 0 6.87-2.73M10.73 2.1C11.15 2.04 11.57 2 12 2c5.52 0 10 4.48 10 10 0 .43-.04.85-.1 1.27M8.53 8.53A3.5 3.5 0 0 0 8 10.5c0 1.93 1.57 3.5 3.5 3.5.74 0 1.42-.23 1.97-.62M2 2l20 20" stroke="#4b5563" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p class="text-sm font-semibold">Gagal memuat data koin</p>
            <p class="text-xs text-gray-600">CoinGecko API mungkin sedang rate limit</p>
            <button onclick="location.reload()"
                class="mt-1 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-full transition flex items-center gap-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2c2.76 0 5.26 1.12 7.07 2.93L22 8" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 2v6h-6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Coba Lagi
            </button>
        </div>
        @endforelse
    </div>
</div>
