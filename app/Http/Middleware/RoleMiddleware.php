<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Maneja la solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Tomar el primer rol (ejemplo: "admin")
        $role = $roles[0] ?? null;

        $user = $request->user();
        if (!$user || !$user->hasRole($role)) {
            abort(403, 'No tiene permiso para acceder a este recurso.');
        }

        return $next($request);
    }
}

