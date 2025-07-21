<?php

use App\Http\Controllers\JsonPlaceholderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;

Route::get('/', function () {
   return view('welcome');
});

