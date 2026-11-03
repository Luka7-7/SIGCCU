<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAlumnoODocente
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(in_array($request->user()?->rol, ['alumno', 'docente'], true), 403);

        return $next($request);
    }
}
