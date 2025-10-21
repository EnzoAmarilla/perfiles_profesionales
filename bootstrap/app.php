<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
         // 🟢 Captura global de errores de autenticación
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'No autenticado. Token inválido o ausente.'
                ], 401);
            }
        });

        // (Opcional) Captura errores 404 y otros
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Recurso no encontrado.'
                ], 404);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Error interno del servidor',
                    'message' => $e->getMessage(), // 🔍 útil en desarrollo
                ], 500);
            }
        });
    })->create();
