<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessLogController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = AccessLog::whereHas('user', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('user');

        // Filtro por usuario (select)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Búsqueda por usuario (nombre/email)
        if ($request->filled('search_user')) {
            $searchTerm = $request->search_user;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filtro por acción/evento (si el campo existe en la tabla)
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtro por rango de fechas
        if ($request->filled('date_from')) {
            $query->where('login_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('login_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Filtro por dirección IP
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        $logs = $query->latest('login_at')->paginate(20)->withQueryString();
        $users = \App\Models\User::where('company_id', $companyId)->get();

        return view('access-logs.index', compact('logs', 'users'));
    }
}
