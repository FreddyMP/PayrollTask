<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthReferente
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('referente')->check()) {
            return redirect()->route('referentes.login');
        }

        return $next($request);
    }
}
