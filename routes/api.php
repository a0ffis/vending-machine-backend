<?php

use App\Http\Controllers\API\MasProductController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(
    '/user',
    function (Request $request) {
        return $request->user();
    }
)->middleware('auth:sanctum');

Route::post('/products', [MasProductController::class, 'storeProduct']);

Route::get('/products', [ProductController::class, 'getProducts']);
