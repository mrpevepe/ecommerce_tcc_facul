<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->cargo === 'administrador') {
            return $next($request);
        }

        abort(403, 'Acesso negado. Apenas administradores podem acessar esta área.');
    }
}