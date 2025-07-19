<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class JsonPlaceholderService
{

    public function getPosts(): array
    {
        return $this->fetchWithCache('/posts');
    }

    public function getUsers(): array
    {
        return $this->fetchWithCache('/users');
    }

    protected function fetchWithCache(string $endpoint, int $ttl = 3600): array
    {
        $cacheKey = 'jsonplaceholder:' . trim($endpoint, '/');

        return Cache::remember($cacheKey, $ttl, function () use ($endpoint) {
            try {
                $response = Http::retry(3, 200)
                    ->timeout(5)
                    ->get(config('services.jsonPlaceholder.base_url') . $endpoint);

                if ($response->successful()) {
                    $data = $this->transformResponse($response->json());

                    Log::info("API [$endpoint] llamada con éxito.", [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                        'fetched' => count($data)
                    ]);

                    return $data;
                }

                Log::warning("API [$endpoint] devolvió error.", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            } catch (Throwable $e) {
                Log::error("Fallo al llamar [$endpoint]: " . $e->getMessage(), [
                    'exception' => $e,
                ]);
                return [];
            }
        });
    }

    protected function transformResponse(array $data): array
    {
        return collect($data)->map(function ($item) {
            return collect($item)->mapWithKeys(function ($value, $key) {
                return [Str::camel($key) => $value];
            })->toArray();
        })->toArray();
    }
}
