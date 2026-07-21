<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     * Redirect to subscription selection page if the company's trial has expired and no plan is selected.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        $company = $user->company;

        if (!$company) {
            return $next($request);
        }

        // If company needs a subscription (trial expired, no plan selected)
        if ($company->needsSubscription()) {
            // Allow access to subscription routes and logout
            if ($request->routeIs('subscription.*') || $request->routeIs('logout') || $request->routeIs('select-company') || $request->routeIs('switch-company')) {
                return $next($request);
            }

            // Redirect to subscription selection
            return redirect()->route('subscription.index');
        }

        return $next($request);
    }
}
