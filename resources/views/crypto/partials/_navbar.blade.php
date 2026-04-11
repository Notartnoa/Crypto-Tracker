<nav class="bg-gray-900 border-b border-gray-800 px-6 py-3 flex items-center justify-between shrink-0">
    <div class="flex items-center">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-8">
    </div>
    <div class="relative w-96">
        <div class="flex items-center bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 gap-2 focus-within:border-blue-500 transition">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                <path d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 22L20 20" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input type="text" id="searchInput"
                placeholder="Cari koin... (contoh: bitcoin, solana)"
                class="w-full bg-transparent text-sm focus:outline-none placeholder-gray-500">
        </div>
        <div id="searchResults"
            class="absolute top-full mt-1 w-full bg-gray-800 border border-gray-700 rounded-lg z-50 hidden overflow-hidden shadow-xl">
        </div>
    </div>
</nav>
