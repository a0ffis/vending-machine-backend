<?php

use App\Http\Controllers\API\Admin\MachineCashController;
use App\Http\Controllers\API\Admin\MasProductController;
use App\Http\Controllers\API\Admin\ProductController as AdminProductController;
use App\Http\Controllers\API\Admin\VendingMachineController;
use App\Http\Controllers\API\Vending\ProductController;
use App\Http\Controllers\API\Vending\PurchaseController;
use App\Http\Middleware\EnsureMachineIdIsValid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(
    '/user',
    function (Request $request) {
        return $request->user();
    }
)->middleware('auth:sanctum');

// Admin Routes
Route::post('/admin/master/products', [MasProductController::class, 'storeProduct']);
Route::post('/admin/create-vending-machine', [VendingMachineController::class, 'createVendingMachine']);
Route::post('/admin/add-product-vending-machine', [AdminProductController::class, 'addProduct'])->middleware(EnsureMachineIdIsValid::class);
Route::post('/admin/update-cash-vending-machine', [MachineCashController::class, 'updateCashInVendingMachine'])
    ->middleware(EnsureMachineIdIsValid::class);
Route::get('/admin/cash-in-vending-machine', [MachineCashController::class, 'cashInVendingMachine'])
    ->middleware(EnsureMachineIdIsValid::class);


// Vending Machine Routes
Route::get('/products', [ProductController::class, 'getProducts'])->middleware(EnsureMachineIdIsValid::class);
Route::post('/purchase', [PurchaseController::class, 'purchaseProduct'])
    ->middleware(EnsureMachineIdIsValid::class);

