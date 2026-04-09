<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CryptoController extends Controller
{
    public function index()
    {
        $coins = $this->fetchCoins();
        return view('crypto.index', compact('coins'));
    }

    private function fetchCoins()
    {
        if (Cache::has('coins_list')) {
            return Cache::get('coins_list');
        }

        $response = Http::timeout(15)
            ->withHeaders(['Accept' => 'application/json'])
            ->get('https://api.coingecko.com/api/v3/coins/markets', [
                'vs_currency'             => 'usd',
                'order'                   => 'market_cap_desc',
                'per_page'                => 20,
                'page'                    => 1,
                'sparkline'               => false,
                'price_change_percentage' => '24h',
            ]);

        if (!$response->successful()) {
            return Cache::get('coins_list', []);
        }

        $data = $response->json();

        if (!is_array($data) || empty($data)) {
            return Cache::get('coins_list', []);
        }

        $coins = collect($data)->filter(function ($coin) {
            return is_array($coin) &&
                isset($coin['id'], $coin['name'], $coin['symbol'],
                      $coin['current_price'], $coin['price_change_percentage_24h'],
                      $coin['market_cap'], $coin['total_volume'], $coin['image']);
        })->values()->toArray();

        if (!empty($coins)) {
            Cache::put('coins_list', $coins, 180);
        }

        return $coins;
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        try {
            $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/search', [
                'query' => $query,
            ]);

            if (!$response->successful()) {
                return response()->json([]);
            }

            $results = collect($response->json()['coins'] ?? [])->take(8);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function chart($id, Request $request)
    {
        $days = $request->get('days', 7);

        try {
            $response = Http::timeout(10)->get("https://api.coingecko.com/api/v3/coins/{$id}/ohlc", [
                'vs_currency' => 'usd',
                'days'        => $days,
            ]);

            if (!$response->successful()) {
                return response()->json([]);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
