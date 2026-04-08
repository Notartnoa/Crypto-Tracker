<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CryptoController extends Controller
{
    public function index()
    {
        $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
            'vs_currency' => 'usd',
            'ids' => 'bitcoin,ethereum,solana,binancecoin,ripple,cardano,dogecoin,polkadot,fartcoin,mantra, ethena, pendle',
            'order'       => 'market_cap_desc',
            'sparkline'   => false,
        ]);

        $coins = $response->json();

        return view('crypto.index', compact('coins'));
    }
}
