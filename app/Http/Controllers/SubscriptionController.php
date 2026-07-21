<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Show the subscription plan selection page.
     */
    public function index()
    {
        $company = Auth::user()->company;

        // If not expired, no need to be here
        if (!$company->needsSubscription() && $company->subscription_plan) {
            return redirect()->route('dashboard');
        }

        $plans = [
            'starter' => [
                'name'  => 'Starter',
                'price' => 'RD$2,000',
                'color' => '#6366f1',
                'icon'  => 'bi-rocket-takeoff',
                'limit' => '10 empleados',
                'features' => [
                    'Dashboard', 'Nómina', 'Empleados', 'Configuraciones',
                    'Empresa', 'Incidencias', 'Registro de Accesos', 'Departamentos',
                ],
                'not_included' => [
                    'Organigrama', 'Fichaje', 'Solicitudes', 'Calendario',
                    'Reglamentos', 'Gestión de Vacaciones', 'Reportes',
                    'Evaluaciones de Personal', 'Proyectos', 'Tablero de Tareas',
                    'Reclutamiento', 'Contratistas', 'Documentos',
                ],
            ],
            'growth' => [
                'name'  => 'Growth',
                'price' => 'RD$4,000',
                'color' => '#10b981',
                'icon'  => 'bi-graph-up-arrow',
                'limit' => '25 empleados',
                'features' => [
                    'Todo lo de Starter', 'Organigrama', 'Fichaje', 'Solicitudes',
                    'Calendario', 'Reglamentos', 'Gestión de Vacaciones',
                ],
                'not_included' => [
                    'Reportes', 'Evaluaciones de Personal', 'Proyectos',
                    'Tablero de Tareas', 'Reclutamiento', 'Contratistas', 'Documentos',
                ],
            ],
            'business' => [
                'name'  => 'Business',
                'price' => 'RD$7,000',
                'color' => '#f59e0b',
                'icon'  => 'bi-briefcase-fill',
                'limit' => '50 empleados',
                'features' => [
                    'Todo lo de Growth', 'Reportes', 'Evaluaciones de Personal',
                    'Proyectos', 'Tablero de Tareas',
                ],
                'not_included' => [
                    'Reclutamiento', 'Contratistas', 'Documentos',
                ],
                'popular' => true,
            ],
            'enterprise' => [
                'name'  => 'Enterprise',
                'price' => 'RD$12,000',
                'color' => '#8b5cf6',
                'icon'  => 'bi-buildings-fill',
                'limit' => '100 empleados',
                'features' => [
                    'Todo lo de Business', 'Reclutamiento', 'Contratistas',
                    'Generación de Documentos', 'Dispositivos',
                    'Acceso completo a todos los módulos',
                ],
                'not_included' => [],
            ],
        ];

        return view('subscription.index', compact('plans', 'company'));
    }

    /**
     * Store the selected subscription plan.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Only the 'super' user can select a plan
        if (!$user->isSuper()) {
            abort(403, 'Solo el usuario Super puede seleccionar un plan de suscripción.');
        }

        $request->validate([
            'plan' => 'required|in:starter,growth,business,enterprise',
        ]);

        $company->update([
            'subscription_plan'         => $request->plan,
            'subscription_selected_at'  => now(),
        ]);

        return redirect()->route('dashboard')->with('success', '¡Plan ' . ucfirst($request->plan) . ' activado exitosamente!');
    }
}
