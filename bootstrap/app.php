<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'throttle.auth' => \App\Http\Middleware\ThrottleAuthRequests::class,
            'sanitize.input' => \App\Http\Middleware\SanitizeInput::class,
        ]);
        
        // Add global middleware for locale setting
        $middleware->web([
            \App\Http\Middleware\SetLocale::class,
        ]);
        
        // Add API rate limiting
        $middleware->api([
            \App\Http\Middleware\ThrottleApiRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                $errors = $e->errors();
                $firstError = collect($errors)->first();
                $firstErrorMessage = is_array($firstError) ? $firstError[0] : $firstError;
                
                return response()->json([
                    'success' => false,
                    'message' => $firstErrorMessage,
                ], 422);
            }
        });
    })->create();
