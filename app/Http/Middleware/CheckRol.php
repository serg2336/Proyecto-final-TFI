<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        // Si el usuario no está logueado, o su rol no coincide con el que pide la ruta, lo rebotamos
        if (!Auth::check() || Auth::user()->rol !== $rol) {
            abort(403, 'No tenés permiso para acceder a esta página.');
        }

        return $next($request);
    }
}