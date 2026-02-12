<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
<<<<<<< HEAD
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
=======
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Configurar autenticación para APIs: devolver JSON en lugar de redirigir
        $middleware->redirectGuestsTo(fn ($request) => 
            $request->expectsJson() 
                ? null  // Para APIs, devuelve 401 JSON
                : route('index')  // Para web, redirige a index
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
>>>>>>> origin/master
        //
    })->create();
