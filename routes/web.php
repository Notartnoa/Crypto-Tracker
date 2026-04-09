<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoController;

Route::get('/', [CryptoController::class, 'index']);
Route::get('/search', [CryptoController::class, 'search']);
Route::get('/chart/{id}', [CryptoController::class, 'chart']);
