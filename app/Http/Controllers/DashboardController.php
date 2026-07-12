<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\UserRequest;
use App\Models\AccessLog;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Vacancy;
use App\Models\Candidate;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        if ($user->role === 'usuario') {
            // Usuario Dashboard Data
            $pendingTasksCount = Task::where('company_id', $companyId)
                ->where('assigned_to', $user->id)
                ->where('status', '!=', 'completed')
                ->count();

            $pendingEvaluations = collect();
            if ($user->employee) {
                $pendingEvaluations = \App\Models\EvaluationAssignment::where('employee_id', $user->employee->id)
                    ->where('is_completed', false)
                    ->with('evaluation')
                    ->get();
            }

            $myRequests = UserRequest::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            $activeRegulations = \App\Models\Regulation::where('company_id', $companyId)
                ->where('is_active', true)
                ->latest()
                ->take(5)
                ->get();

            // Today's events
            $todayEvents = CalendarEvent::with('links')
                ->where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->whereDate('event_date', Carbon::today())
                ->orderBy('event_time')
                ->get();

            $showTodayModal = false;
            if (!session()->has('today_modal_shown') && $todayEvents->isNotEmpty()) {
                $showTodayModal = true;
                session()->put('today_modal_shown', true);
            } else if (session()->has('just_logged_in') && $todayEvents->isNotEmpty()) {
                $showTodayModal = true;
                session()->forget('just_logged_in');
                session()->put('today_modal_shown', true);
            }

            return view('dashboard.usuario', compact(
                'pendingTasksCount', 'pendingEvaluations', 'myRequests', 
                'activeRegulations', 'todayEvents', 'showTodayModal'
            ));
        }

        // ─── KPI Cards ───────────────────────────────────────
        $totalEmployees = Employee::where('company_id', $companyId)->count();

        // Latest payroll period & total
        $latestPeriod = Payroll::where('company_id', $companyId)
            ->orderByDesc('period')
            ->value('period');

        $payrollSummaryLatest = null;
        if ($latestPeriod) {
            $payrollSummaryLatest = Payroll::where('company_id', $companyId)
                ->where('period', $latestPeriod)
                ->selectRaw('
                    COUNT(*) as total_records,
                    SUM(gross_salary) as total_gross,
                    SUM(deductions) as total_deductions,
                    SUM(net_salary) as total_net,
                    SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count
                ')
                ->first();
        }

        $openVacancies = Vacancy::where('company_id', $companyId)
            ->where('status', 'open')
            ->count();

        $activeCandidates = Candidate::whereHas('vacancy', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->where('status', 'active')->count();

        // ─── Payroll History (last 6 periods) ────────────────
        $payrollHistory = Payroll::where('company_id', $companyId)
            ->select('period')
            ->selectRaw('COUNT(*) as employee_count')
            ->selectRaw('SUM(gross_salary) as total_gross')
            ->selectRaw('SUM(deductions) as total_deductions')
            ->selectRaw('SUM(net_salary) as total_net')
            ->selectRaw('SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_count')
            ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count')
            ->groupBy('period')
            ->orderByDesc('period')
            ->take(6)
            ->get();

        // ─── Salary Statistics ───────────────────────────────
        $salaryStats = Employee::where('company_id', $companyId)
            ->selectRaw('MIN(salary) as min_salary, MAX(salary) as max_salary, AVG(salary) as avg_salary')
            ->first();

        // Average deductions breakdown from latest period
        $deductionBreakdown = null;
        if ($latestPeriod) {
            $deductionBreakdown = Payroll::where('company_id', $companyId)
                ->where('period', $latestPeriod)
                ->selectRaw('AVG(ars) as avg_ars, AVG(afp) as avg_afp, AVG(isr) as avg_isr, AVG(descuentos) as avg_otros')
                ->first();
        }

        // ─── Department distribution ─────────────────────────
        $departmentDistribution = Employee::where('company_id', $companyId)
            ->select('department', DB::raw('COUNT(*) as count'))
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->groupBy('department')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        // ─── Recruitment Pipeline ────────────────────────────
        $vacancyPipeline = Vacancy::where('company_id', $companyId)
            ->where('status', 'open')
            ->withCount(['candidates', 'candidates as hired_count' => function ($q) {
                $q->where('status', 'hired');
            }, 'candidates as discarded_count' => function ($q) {
                $q->where('status', 'discarded');
            }, 'steps'])
            ->latest()
            ->take(5)
            ->get();

        // Recent candidates (last 5)
        $recentCandidates = Candidate::whereHas('vacancy', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('vacancy')
            ->latest()
            ->take(5)
            ->get();

        // Recent hires
        $recentHires = Candidate::whereHas('vacancy', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->where('status', 'hired')
            ->with('vacancy')
            ->latest('updated_at')
            ->take(5)
            ->get();

        // ─── HR Requests breakdown ───────────────────────────
        $requestsByType = UserRequest::where('company_id', $companyId)
            ->select('type', 'status', DB::raw('COUNT(*) as count'))
            ->groupBy('type', 'status')
            ->get()
            ->groupBy('type')
            ->map(function ($items) {
                return $items->pluck('count', 'status');
            });

        $pendingRequests = UserRequest::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        // ─── Recent Access (keep existing) ───────────────────
        $recentAccess = AccessLog::whereHas('user', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('user')->latest('login_at')->take(5)->get();

        // ─── Today's Events (keep existing) ──────────────────
        $todayEvents = CalendarEvent::with('links')
            ->where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->whereDate('event_date', Carbon::today())
            ->orderBy('event_time')
            ->get();

        $showTodayModal = false;
        if (!session()->has('today_modal_shown') && $todayEvents->isNotEmpty()) {
            $showTodayModal = true;
            session()->put('today_modal_shown', true);
        } else if (session()->has('just_logged_in') && $todayEvents->isNotEmpty()) {
            $showTodayModal = true;
            session()->forget('just_logged_in');
            session()->put('today_modal_shown', true);
        }

        // ─── Payroll Configuration Modal ──────────────────────────
        // Si el usuario es 'super' y la empresa aún no tiene configuración de nómina completa,
        // mostramos el modal obligatorio de selección (no se puede cerrar sin elegir).
        $showPayrollFrequencyModal = $user->isSuper()
            && (is_null($user->company->payroll_frequency ?? null)
                || is_null($user->company->bonus_payment_method ?? null));

        // ─── Task stats (keep for compatibility) ─────────────
        $taskStats = [
            'pending' => Task::where('company_id', $companyId)->where('status', 'pending')->count(),
            'in_progress' => Task::where('company_id', $companyId)->where('status', 'in_progress')->count(),
            'completed' => Task::where('company_id', $companyId)->where('status', 'completed')->count(),
        ];

        return view('dashboard.index', compact(
            'totalEmployees', 'latestPeriod', 'payrollSummaryLatest',
            'openVacancies', 'activeCandidates',
            'payrollHistory', 'salaryStats', 'deductionBreakdown',
            'departmentDistribution',
            'vacancyPipeline', 'recentCandidates', 'recentHires',
            'requestsByType', 'pendingRequests',
            'recentAccess', 'todayEvents', 'showTodayModal',
            'taskStats', 'showPayrollFrequencyModal'
        ));
    }
}
