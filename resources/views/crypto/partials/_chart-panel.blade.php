<div id="chartSidePanel" class="hidden w-1/2 p-6 flex-col overflow-hidden border-l border-gray-800">
    <div class="flex items-center gap-4 mb-4 shrink-0">
        <img id="chartCoinImg" src="" class="w-10 h-10 rounded-full">
        <div>
            <h2 id="chartCoinName" class="text-lg font-bold leading-tight"></h2>
            <p id="chartCoinSymbol" class="text-gray-400 text-xs uppercase font-semibold tracking-wider"></p>
        </div>
        <div class="ml-auto text-right flex items-start gap-4">
            <div>
                <p id="chartCoinPrice" class="text-2xl font-bold"></p>
                <p id="chartCoinChange" class="text-xs font-semibold flex items-center justify-end gap-1"></p>
            </div>
            <button onclick="closeChart()" class="text-gray-500 hover:text-white mt-1 transition">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 22c5.5 0 10-4.5 10-10S17.5 2 12 2 2 6.5 2 12s4.5 10 10 10zM9.17 14.83l5.66-5.66M14.83 14.83L9.17 9.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>
    <div class="flex gap-2 mb-4 shrink-0">
        <button onclick="changeRange(1)"  class="range-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-400" data-range="1">1H</button>
        <button onclick="changeRange(7)"  class="range-btn px-3 py-1 rounded-full text-xs font-medium bg-blue-600 text-white" data-range="7">7D</button>
        <button onclick="changeRange(14)" class="range-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-400" data-range="14">14D</button>
        <button onclick="changeRange(30)" class="range-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-400" data-range="30">30D</button>
    </div>
    <div class="bg-gray-900 rounded-2xl p-4 flex-1 flex flex-col overflow-hidden">
        <p class="text-xs text-gray-500 font-medium mb-3 shrink-0">Candlestick Chart (OHLC)</p>
        <div id="chartLoading" class="hidden flex-1 items-center justify-center gap-2 text-gray-500 text-sm">
            <svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Memuat chart...
        </div>
        <div id="chartError" class="hidden flex-1 flex-col items-center justify-center text-gray-500 gap-2">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                <path d="M12 7.75v5M21.08 8.58v6.84c0 1.12-.6 2.16-1.57 2.73l-5.94 3.43c-.97.56-2.17.56-3.15 0L4.49 18.15a3.15 3.15 0 0 1-1.57-2.73V8.58c0-1.12.6-2.16 1.57-2.73l5.94-3.43c.97-.56 2.17-.56 3.15 0l5.94 3.43c.97.57 1.56 1.6 1.56 2.73zM12 16.2v.5" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p class="text-sm font-medium">Gagal memuat chart</p>
        </div>
        <div class="flex-1 relative" id="chartWrapper">
            <canvas id="priceChart" class="absolute inset-0 w-full h-full"></canvas>
        </div>
    </div>
</div>
