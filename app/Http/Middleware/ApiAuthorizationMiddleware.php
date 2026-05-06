<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = env('X_AUTHORIZATION');
        $providedToken = $request->header('x-authorization');

        if (!$expectedToken || $providedToken !== $expectedToken) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing x-authorization header'
            ], 401);
        }

        return $next($request);
    }
}
