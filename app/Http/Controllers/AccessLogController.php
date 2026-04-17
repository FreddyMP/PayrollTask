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

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->where('login_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('login_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->latest('login_at')->paginate(20);
        $users = \App\Models\User::where('company_id', $companyId)->get();

        return view('access-logs.index', compact('logs', 'users'));
    }
}
