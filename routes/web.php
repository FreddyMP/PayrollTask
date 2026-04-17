<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AccessLogController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PasswordResetController;

// Auth routes
Route::get('/', function () {
    return redirect()->route('login'); });
Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
Route::post('/login', [AuthController::class , 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

    // Tasks
    Route::resource('tasks', TaskController::class)->except('show');
    Route::resource('devices', \App\Http\Controllers\DeviceController::class)->middleware('role:supervisor');
    Route::patch('/tasks/{task}/status', [TaskController::class , 'updateStatus'])->name('tasks.updateStatus');
    Route::delete('/tasks/attachments/{attachment}', [TaskController::class, 'destroyAttachment'])->name('tasks.attachments.destroy');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/team', [ProjectController::class, 'updateTeam'])->name('projects.updateTeam');

    // Calendar
    Route::resource('calendar', CalendarController::class)->except('show');
    Route::get('/api/calendar/events', [CalendarController::class, 'apiEvents'])->name('calendar.apiEvents');

    // Access Logs (Admin+)
    Route::get('/access-logs', [AccessLogController::class , 'index'])
        ->name('access-logs.index')
        ->middleware('role:admin');

    // Requests
    Route::get('/requests', [RequestController::class , 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class , 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class , 'store'])->name('requests.store');
    Route::patch('/requests/{userRequest}/review', [RequestController::class , 'review'])
        ->name('requests.review')
        ->middleware('role:supervisor');
    Route::delete('/requests/{userRequest}', [RequestController::class , 'destroy'])->name('requests.destroy');

    // Employees (Admin+)
    Route::middleware('role:admin')->group(function () {
            Route::resource('employees', EmployeeController::class);
        }
        );

        // Payroll (Admin+)
        Route::middleware('role:admin')->group(function () {
            Route::get('/payroll', [PayrollController::class , 'index'])->name('payroll.index');
            Route::get('/payroll/bonuses', [PayrollController::class , 'bonuses'])->name('payroll.bonuses');
            Route::get('/payroll/create', [PayrollController::class , 'create'])->name('payroll.create');
            Route::post('/payroll', [PayrollController::class , 'store'])->name('payroll.store');
            Route::patch('/payroll/{payroll}/paid', [PayrollController::class , 'markPaid'])->name('payroll.markPaid');
            Route::delete('/payroll/{payroll}', [PayrollController::class , 'destroy'])->name('payroll.destroy');
            Route::get('/api/payroll/overtime', [PayrollController::class, 'apiOvertimeData'])->name('payroll.apiOvertime');
        }
        );

        // Settings
        Route::get('/settings', [SettingsController::class , 'index'])->name('settings.index');
        Route::post('/settings/password', [SettingsController::class , 'updatePassword'])->name('settings.password');
        Route::post('/settings/email', [SettingsController::class , 'updateEmail'])->name('settings.email');
        Route::post('/settings/profile', [SettingsController::class , 'updateProfile'])->name('settings.profile');

        // Reports (Admin+)
        Route::middleware('role:admin')->group(function () {
            Route::get('/reports', [ReportController::class , 'index'])->name('reports.index');
            Route::get('/reports/payroll', [ReportController::class , 'payroll'])->name('reports.payroll');
            Route::get('/reports/tasks', [ReportController::class , 'tasks'])->name('reports.tasks');
            Route::get('/reports/access', [ReportController::class , 'access'])->name('reports.access');
            Route::get('/api/reports/chart/{type}', [ReportController::class , 'apiChartData'])->name('reports.chart');
        }
        );

        // Company settings (Super only)
        Route::middleware('role:super')->group(function () {
            Route::get('/company', [CompanyController::class , 'edit'])->name('company.edit');
            Route::post('/company', [CompanyController::class , 'update'])->name('company.update');
        }
        );    });
