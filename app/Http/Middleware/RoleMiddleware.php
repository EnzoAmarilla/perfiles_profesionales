<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user || !in_array($user->userType->name, $roles)) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta ruta.'], 403);
        }

        return $next($request);
    }
}