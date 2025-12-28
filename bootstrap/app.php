<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use TestFlowLabs\PestPluginBridge\Laravel\BridgeHttpFakeMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Token-based API auth doesn't need stateful middleware

        // Enable HTTP faking for browser tests (only in testing environment)
        if (app()->environment('testing')) {
            $middleware->prepend(BridgeHttpFakeMiddleware::class);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
