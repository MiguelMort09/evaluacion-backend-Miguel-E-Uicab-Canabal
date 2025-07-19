<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiException extends Exception
{

    public function handle(Request $request, Throwable $exception): JsonResponse
    {
        $statusCode =  500;
        $message =  '!Ups, algo salió mal!';
        $errors = null;

        if ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'Error de validación';
            $errors = $exception->validator->errors()->getMessages();
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'Recurso no encontrado';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $statusCode = 405;
            $message = 'Método no permitido';
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $statusCode = 403;
            $message = 'Acceso denegado';
        } elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } else {
            Log::error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
            'debug' => config('app.debug') ? [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ] : null
        ], $statusCode);
    }
}
