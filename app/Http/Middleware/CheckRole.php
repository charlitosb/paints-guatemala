<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión');
        }

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Verificar si el usuario tiene alguno de los roles permitidos
        if (!in_array($user->role, $roles)) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        return $next($request);
    }
}