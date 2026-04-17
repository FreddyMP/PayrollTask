<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\UserRequest;
use App\Models\AccessLog;
use App\Models\Employee;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $taskStats = [
            'pending' => Task::where('company_id', $companyId)->where('status', 'pending')->count(),
            'in_progress' => Task::where('company_id', $companyId)->where('status', 'in_progress')->count(),
            'review' => Task::where('company_id', $companyId)->where('status', 'review')->count(),
            'completed' => Task::where('company_id', $companyId)->where('status', 'completed')->count(),
        ];

        $pendingRequests = UserRequest::where('company_id', $companyId)->where('status', 'pending')->count();
        $totalEmployees = Employee::where('company_id', $companyId)->count();

        $recentTasks = Task::where('company_id', $companyId)
            ->with(['assignedUser', 'creator'])
            ->latest()
            ->take(5)
            ->get();

        $recentAccess = AccessLog::whereHas('user', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('user')->latest('login_at')->take(5)->get();

        // Today's calendar events for the logged-in user
        // Using Carbon::today() which should now match the Santo Domingo timezone
        $todayEvents = CalendarEvent::with('links')
            ->where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->whereDate('event_date', \Carbon\Carbon::today())
            ->orderBy('event_time')
            ->get();

        // Show modal on first dashboard visit of this session if there are events today
        $showTodayModal = false;
        if (!session()->has('today_modal_shown') && $todayEvents->isNotEmpty()) {
            $showTodayModal = true;
            session()->put('today_modal_shown', true);
        } else if (session()->has('just_logged_in') && $todayEvents->isNotEmpty()) {
            // Fallback for previous logout-based flash just in case
            $showTodayModal = true;
            session()->forget('just_logged_in');
            session()->put('today_modal_shown', true);
        }

        return view('dashboard.index', compact(
            'taskStats', 'pendingRequests', 'totalEmployees',
            'recentTasks', 'recentAccess', 'todayEvents', 'showTodayModal'
        ));
    }
}
