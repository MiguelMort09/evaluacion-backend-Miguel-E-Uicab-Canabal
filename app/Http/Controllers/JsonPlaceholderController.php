<?php

namespace App\Http\Controllers;

use App\Services\JsonPlaceholderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JsonPlaceholderController extends Controller
{
    public function posts(JsonPlaceholderService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $service->getPosts(),
        ]);
    }

    public function users(JsonPlaceholderService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $service->getUsers(),
        ]);
    }
}
