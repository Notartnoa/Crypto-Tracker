<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CryptoController extends Controller
{
    // Halaman utama
    public function index()
    {
        $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
            'vs_currency'           => 'usd',
            'order'                 => 'market_cap_desc',
            'per_page'              => 20,
            'page'                  => 1,
            'sparkline'             => false,
            'price_change_percentage' => '24h',
        ]);

        $coins = $response->json();

        return view('crypto.index', compact('coins'));
    }

    // Search koin (dipanggil via AJAX)
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $response = Http::get('https://api.coingecko.com/api/v3/search', [
            'query' => $query,
        ]);

        $results = collect($response->json()['coins'] ?? [])->take(8);

        return response()->json($results);
    }

    public function chart($id, Request $request)
    {
        $days = $request->get('days', 7);

        $response = Http::get("https://api.coingecko.com/api/v3/coins/{$id}/ohlc", [
            'vs_currency' => 'usd',
            'days'        => $days,
        ]);

        return response()->json($response->json());
    }
}
