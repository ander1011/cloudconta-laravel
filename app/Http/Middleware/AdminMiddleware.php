<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        $user = auth()->user();

        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua conta foi desativada.');
        }

        if (!$user->isAdmin()) {
            return redirect()->route('user.dashboard')->with('error', 'Acesso negado. Área administrativa.');
        }

        return $next($request);
    }
}
