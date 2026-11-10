<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Permite el acceso solo a los roles indicados.
     * Uso en rutas: ->middleware('role:administrador,vendedor')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAnyRole(...$roles)) {
            abort(403, 'No tenés permiso para acceder a este módulo.');
        }

        return $next($request);
    }
}
