<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'Warehouse API is running',
        'documentation' => 'See README.md for usage',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});
