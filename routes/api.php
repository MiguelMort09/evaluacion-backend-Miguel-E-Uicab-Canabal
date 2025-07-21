<?php

use App\Http\Controllers\JsonPlaceholderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;

Route::middleware('guest')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('users', [UserController::class, 'store']);
});
// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('products', ProductController::class);
});

Route::get('/external/posts', [JsonPlaceholderController::class, 'posts']);
Route::get('/external/users', [JsonPlaceholderController::class, 'users']);
Route::post('/webhooks/notifications', [\App\Http\Controllers\NotificationController::class, 'handleWebhook']);

