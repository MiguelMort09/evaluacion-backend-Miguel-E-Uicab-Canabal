<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'request.logging' => \App\Http\Middleware\RequestLogging::class,
        ]);
        $middleware->group('api', [
            'request.logging',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $throwable, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return app(\App\Exceptions\ApiException::class)->handle($request, $throwable);
            }
            return null;
        });
    })->create();
