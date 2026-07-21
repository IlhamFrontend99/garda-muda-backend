<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__."/../routes/web.php",
        api: __DIR__."/../routes/api.php",
        commands: __DIR__."/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function ($response, Throwable $e, $request) {
            return response()->json([
                "error" => true,
                "message" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ], $response->getStatusCode() >= 400 ? $response->getStatusCode() : 500);
        });
    })->create();