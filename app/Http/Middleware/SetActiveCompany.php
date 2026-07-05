<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetActiveCompany
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $activeCompanyId = (int) session('active_company_id') ?: null;

            // If no company selected in session, check how many companies the user belongs to
            if (!$activeCompanyId) {
                // Find all companies this user belongs to via the employees table
                $companies = \App\Models\Employee::where('user_id', $user->id)
                    ->whereHas('company', function ($q) {
                        $q->where('status', 'active');
                    })
                    ->with('company')
                    ->get();

                // Also check the primary company_id on the user (for super/admin users not in employees)
                $primaryCompany = $user->company;
                
                if ($companies->count() === 0 && $primaryCompany) {
                    // User only has their primary company (super/admin not in employees table)
                    session(['active_company_id' => (int) $user->company_id]);
                    $activeCompanyId = (int) $user->company_id;
                } elseif ($companies->count() === 1) {
                    // Only one company — auto-select it
                    $companyId = (int) $companies->first()->company_id;
                    session(['active_company_id' => $companyId]);
                    $activeCompanyId = $companyId;
                } elseif ($companies->count() > 1) {
                    // Multiple companies — redirect to selector (unless already there)
                    if (!$request->routeIs('select-company') && !$request->routeIs('select-company.post')) {
                        return redirect()->route('select-company');
                    }
                    return $next($request);
                }
            }

            // Inject the active company_id and role into the user object via setAttribute
            if ($activeCompanyId) {
                $activeEmployee = \App\Models\Employee::where('user_id', $user->id)
                    ->where('company_id', $activeCompanyId)
                    ->first();

                if ($activeEmployee && !empty($activeEmployee->role)) {
                    // Use setAttribute so Eloquent's getAttribute() always returns this value
                    $user->setAttribute('role', $activeEmployee->role);
                    $user->setAttribute('company_id', $activeCompanyId);
                } else {
                    $user->setAttribute('company_id', $activeCompanyId);
                }
            }
        }

        return $next($request);
    }
}
