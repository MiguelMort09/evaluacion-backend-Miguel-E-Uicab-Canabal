<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogging
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Registrar información antes de la solicitud
        $startTime = microtime(true);

        // Obtener el token del usuario si existe
        $token = $request->bearerToken();
        $userId = null;

        if ($token && auth('sanctum')->user()) {
            $userId = auth('sanctum')->id();
        }

        // Registrar la solicitud entrante
        Log::info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_id' => $userId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $this->filterHeaders($request->headers->all()),
            'payload' => $this->filterSensitiveData($request->all()),
        ]);

        // Procesar la solicitud
        $response = $next($request);

        // Calcular el tiempo de respuesta
        $duration = microtime(true) - $startTime;

        // Registrar la respuesta
        Log::info('API Response', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_id' => $userId,
            'status' => $response->status(),
            'duration' => round($duration * 1000, 2) . 'ms', // Convertir a milisegundos
            'response' => $this->filterSensitiveData($this->getResponseContent($response)),
        ]);

        return $response;
    }

    /**
     * Filtra datos sensibles de la solicitud/respuesta
     */
    private function filterSensitiveData(array|string $data): array|string
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            } else {
                return $data;
            }
        }

        $sensitiveFields = [
            'password',
            'password_confirmation',
            'credit_card',
            'token',
        ];

        return collect($data)->map(function ($value, $key) use ($sensitiveFields) {
            if (in_array($key, $sensitiveFields)) {
                return '********';
            }

            if (is_array($value)) {
                return $this->filterSensitiveData($value);
            }

            return $value;
        })->all();
    }

    /**
     * Filtra headers sensibles
     */
    private function filterHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization',
            'cookie',
            'x-xsrf-token',
        ];

        return collect($headers)->map(function ($value, $key) use ($sensitiveHeaders) {
            if (in_array(strtolower($key), $sensitiveHeaders)) {
                return ['********'];
            }
            return $value;
        })->all();
    }

    /**
     * Obtiene el contenido de la respuesta
     */
    private function getResponseContent($response): array|string
    {
        if (method_exists($response, 'content')) {
            $content = $response->content();

            if (is_string($content)) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }

            return $content;
        }

        return [];
    }
}
