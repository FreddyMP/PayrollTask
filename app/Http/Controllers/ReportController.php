<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\AccessLog;
use App\Models\Payroll;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function payroll(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Payroll::where('company_id', $companyId)->with('employee.user');

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $payrolls = $query->get();
        $totalGross = $payrolls->sum('gross_salary');
        $totalDeductions = $payrolls->sum('deductions');
        $totalNet = $payrolls->sum('net_salary');

        return view('reports.payroll', compact('payrolls', 'totalGross', 'totalDeductions', 'totalNet'));
    }

    public function tasks(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Task::where('company_id', $companyId);

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $tasks = $query->get();
        $statusCounts = $tasks->groupBy('status')->map->count();
        $priorityCounts = $tasks->groupBy('priority')->map->count();

        $userTasks = Task::where('company_id', $companyId)
            ->select('assigned_to', DB::raw('count(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"))
            ->groupBy('assigned_to')
            ->with('assignedUser')
            ->get();

        return view('reports.tasks', compact('tasks', 'statusCounts', 'priorityCounts', 'userTasks'));
    }

    public function access(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = AccessLog::whereHas('user', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('user');

        if ($request->filled('date_from')) {
            $query->where('login_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('login_at', '<=', $request->date_to . ' 23:59:59');
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->latest('login_at')->get();

        $userStats = $logs->groupBy('user_id')->map(function ($group) {
            return [
            'user' => $group->first()->user,
            'total_sessions' => $group->count(),
            'last_login' => $group->first()->login_at,
            ];
        });

        $users = User::where('company_id', $companyId)->get();

        return view('reports.access', compact('logs', 'userStats', 'users'));
    }

    public function apiChartData(Request $request, string $type)
    {
        $companyId = Auth::user()->company_id;

        if ($type === 'tasks') {
            $data = Task::where('company_id', $companyId)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
            return response()->json($data);
        }

        if ($type === 'payroll') {
            $data = Payroll::where('company_id', $companyId)
                ->select('period', DB::raw('SUM(net_salary) as total'))
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('total', 'period');
            return response()->json($data);
        }

        if ($type === 'access') {
            $data = AccessLog::whereHas('user', fn($q) => $q->where('company_id', $companyId))
                ->select(DB::raw("strftime('%Y-%m-%d', login_at) as date"), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date');
            return response()->json($data);
        }

        return response()->json([]);
    }
}
