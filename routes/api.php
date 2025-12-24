<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

// List all products
Route::get('/products', [ProductController::class, 'index']);

// Login
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Create new order
    Route::post('/orders', [OrderController::class, 'store']);

    // Get order by id
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
