<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', App\Http\Controllers\Api\UserController::class)
    ->only(['store']);

// Rutas de documentación

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
});
