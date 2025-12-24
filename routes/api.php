<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

// List all products
Route::get('/products', [ProductController::class, 'index']);

// Login
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum','throttle:api')->group(function () {

    Route::controller(OrderController::class)->group(function () {
        // Create new order
        Route::post('/orders', 'store');
        // Get order by id
        Route::get('/orders/{order}', 'show');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
