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
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\CompanyFieldController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\OrgChartController;
use App\Http\Controllers\RegulationController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\FichajeController;

// Auth routes
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Company Selection (multi-company users)
    Route::get('/select-company', [AuthController::class, 'showSelectCompany'])->name('select-company');
    Route::post('/select-company', [AuthController::class, 'selectCompany'])->name('select-company.post');
    Route::post('/switch-company', [AuthController::class, 'switchCompany'])->name('switch-company');

    // Tasks
    Route::resource('tasks', TaskController::class)->except('show');
    Route::resource('devices', \App\Http\Controllers\DeviceController::class)->middleware('role:supervisor');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::delete('/tasks/attachments/{attachment}', [TaskController::class, 'destroyAttachment'])->name('tasks.attachments.destroy');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/team', [ProjectController::class, 'updateTeam'])->name('projects.updateTeam');

    // Calendar
    Route::resource('calendar', CalendarController::class)->except('show');
    Route::get('/api/calendar/events', [CalendarController::class, 'apiEvents'])->name('calendar.apiEvents');
    Route::get('/api/calendar/holidays', [CalendarController::class, 'apiHolidays'])->name('calendar.apiHolidays');
    Route::post('/api/calendar/holidays/toggle', [CalendarController::class, 'toggleHoliday'])->name('calendar.toggleHoliday');
    Route::post('/api/calendar/rest-days/toggle', [CalendarController::class, 'toggleWeekendRest'])->name('calendar.toggleWeekendRest');

    // Access Logs (Admin+)
    Route::get('/access-logs', [AccessLogController::class, 'index'])
        ->name('access-logs.index')
        ->middleware('role:admin');

    // Requests
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::patch('/requests/{userRequest}/review', [RequestController::class, 'review'])
        ->name('requests.review')
        ->middleware('role:supervisor');
    Route::delete('/requests/{userRequest}', [RequestController::class, 'destroy'])->name('requests.destroy');

    // Fichajes
    Route::get('/fichajes', [FichajeController::class, 'index'])->name('fichajes.index');

    // Employees (Admin+)
    Route::middleware('role:admin')->group(
        function () {
            Route::resource('employees', EmployeeController::class);
            Route::delete('/employees/documents/{document}', [EmployeeController::class, 'destroyDocument'])->name('employees.documents.destroy');
        }
    );

    // Payroll (Admin+)
    Route::middleware('role:admin')->group(
        function () {
            Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
            Route::get('/payroll/bonuses', [PayrollController::class, 'bonuses'])->name('payroll.bonuses');
            Route::post('/payroll/bonuses/add-to-payroll', [PayrollController::class, 'addBonusesToPayroll'])->name('payroll.bonuses.addToPayroll');
            Route::post('/payroll/bonuses/{employee}/pay-separate', [PayrollController::class, 'paySeparateBonus'])->name('payroll.bonuses.paySeparate');
            Route::get('/payroll/benefits', [PayrollController::class, 'benefits'])->name('payroll.benefits');
            Route::get('/payroll/christmas', [PayrollController::class, 'christmas'])->name('payroll.christmas');
            Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
            Route::get('/payroll/tss', [PayrollController::class, 'tss'])->name('payroll.tss');
            Route::get('/payroll/ir17', [PayrollController::class, 'ir17'])->name('payroll.ir17');
            Route::post('/payroll/christmas/{employee}/pay', [PayrollController::class, 'payChristmas'])->name('payroll.christmas.pay');
            Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
            Route::post('/payroll/auto-generate', [PayrollController::class, 'autoGenerate'])->name('payroll.autoGenerate');
            Route::get('/payroll/{payroll}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
            Route::patch('/payroll/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
            Route::patch('/payroll/{payroll}/paid', [PayrollController::class, 'markPaid'])->name('payroll.markPaid');
            Route::post('/payroll/mark-all-paid', [PayrollController::class, 'markAllPaid'])->name('payroll.markAllPaid');
            Route::delete('/payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
            Route::get('/api/payroll/overtime', [PayrollController::class, 'apiOvertimeData'])->name('payroll.apiOvertime');
        }
    );

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');

    // Reports (Admin+)
    Route::middleware('role:admin')->group(
        function () {
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');
            Route::get('/reports/tasks', [ReportController::class, 'tasks'])->name('reports.tasks');
            Route::get('/reports/access', [ReportController::class, 'access'])->name('reports.access');
            Route::get('/api/reports/chart/{type}', [ReportController::class, 'apiChartData'])->name('reports.chart');
        }
    );

    // Company settings (Super only)
    Route::middleware('role:super')->group(function () {
        Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
        Route::post('/company', [CompanyController::class, 'update'])->name('company.update');
        Route::post('/company/payroll-frequency', [CompanyController::class, 'updatePayrollFrequency'])->name('company.payrollFrequency');
        Route::post('/company/logo/delete', [CompanyController::class, 'deleteLogo'])->name('company.deleteLogo');
    });

    // Recruitment
    Route::prefix('recruitment')->name('recruitment.')->group(function () {
        Route::get('/', [RecruitmentController::class, 'index'])->name('index');
        Route::post('/', [RecruitmentController::class, 'store'])->name('store');
        Route::get('/{vacancy}', [RecruitmentController::class, 'show'])->name('show');
        Route::post('/{vacancy}/steps', [RecruitmentController::class, 'addStep'])->name('steps.store');
        Route::patch('/steps/{step}', [RecruitmentController::class, 'updateStep'])->name('steps.update');
        Route::post('/{vacancy}/candidates', [RecruitmentController::class, 'addCandidate'])->name('candidates.store');
        Route::post('/candidates/{candidate}/progress', [RecruitmentController::class, 'updateProgress'])->name('candidates.progress');
        Route::get('/{vacancy}/ranking', [RecruitmentController::class, 'ranking'])->name('ranking');

        // Application Form
        Route::post('/application-form/{applicationForm}/fields', [RecruitmentController::class, 'storeField'])->name('application-form.fields.store');
        Route::delete('/application-form/fields/{field}', [RecruitmentController::class, 'deleteField'])->name('application-form.fields.destroy');
        Route::get('/application-form/{applicationForm}/print', [RecruitmentController::class, 'printForm'])->name('application-form.print');

        // Hiring & Closing
        Route::post('/candidates/{candidate}/hire', [RecruitmentController::class, 'hireCandidate'])->name('candidates.hire');
        Route::post('/vacancies/{vacancy}/close', [RecruitmentController::class, 'closeVacancy'])->name('vacancies.close');
    });

    // Company Fields (Global Variables)
    Route::prefix('company-fields')->name('company-fields.')->group(function () {
        Route::get('/', [CompanyFieldController::class, 'index'])->name('index');
        Route::post('/', [CompanyFieldController::class, 'store'])->name('store');
        Route::patch('/{field}', [CompanyFieldController::class, 'update'])->name('update');
        Route::delete('/{field}', [CompanyFieldController::class, 'destroy'])->name('destroy');
    });

    // Documents & Templates
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{template}', [DocumentController::class, 'show'])->name('show');
        Route::post('/{template}/generate', [DocumentController::class, 'generate'])->name('generate');
        Route::delete('/{template}', [DocumentController::class, 'destroy'])->name('destroy');
    });

    // Evaluations (Admin/Super)
    Route::middleware('role:admin')->prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EvaluationController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\EvaluationController::class, 'store'])->name('store');
        Route::get('/{evaluation}/show', [\App\Http\Controllers\EvaluationController::class, 'show'])->name('show');
        Route::patch('/{evaluation}', [\App\Http\Controllers\EvaluationController::class, 'update'])->name('update');
        Route::delete('/{evaluation}', [\App\Http\Controllers\EvaluationController::class, 'destroy'])->name('destroy');

        Route::post('/{evaluation}/questions', [\App\Http\Controllers\EvaluationController::class, 'storeQuestion'])->name('questions.store');
        Route::delete('/{evaluation}/questions/{question}', [\App\Http\Controllers\EvaluationController::class, 'destroyQuestion'])->name('questions.destroy');

        Route::post('/{evaluation}/assignments', [\App\Http\Controllers\EvaluationController::class, 'storeAssignments'])->name('assignments.store');
        Route::delete('/{evaluation}/assignments/{assignment}', [\App\Http\Controllers\EvaluationController::class, 'destroyAssignment'])->name('assignments.destroy');

        Route::get('/{evaluation}/results', [\App\Http\Controllers\EvaluationController::class, 'results'])->name('results');
    });

    // Evaluations (Employee)
    Route::get('/evaluations/{evaluation}/fill', [\App\Http\Controllers\EvaluationController::class, 'fill'])->name('evaluations.fill');
    Route::post('/evaluations/{evaluation}/submit', [\App\Http\Controllers\EvaluationController::class, 'submit'])->name('evaluations.submit');

    // Contractors (Admin+)
    Route::middleware('role:admin,super')->prefix('contractors')->name('contractors.')->group(function () {
        Route::get('/', [ContractorController::class, 'index'])->name('index');
        Route::get('/create', [ContractorController::class, 'create'])->name('create');
        Route::post('/', [ContractorController::class, 'store'])->name('store');
        Route::get('/{contractor}/edit', [ContractorController::class, 'edit'])->name('edit');
        Route::patch('/{contractor}', [ContractorController::class, 'update'])->name('update');
        Route::delete('/{contractor}', [ContractorController::class, 'destroy'])->name('destroy');

        // Invoices
        Route::get('/invoices', [ContractorController::class, 'invoicesIndex'])->name('invoices.index');
        Route::get('/invoices/create', [ContractorController::class, 'invoicesCreate'])->name('invoices.create');
        Route::post('/invoices', [ContractorController::class, 'invoicesStore'])->name('invoices.store');
        Route::get('/invoices/{invoice}/edit', [ContractorController::class, 'invoicesEdit'])->name('invoices.edit');
        Route::patch('/invoices/{invoice}', [ContractorController::class, 'invoicesUpdate'])->name('invoices.update');
        Route::delete('/invoices/{invoice}', [ContractorController::class, 'invoicesDestroy'])->name('invoices.destroy');
    });

    // Departments (Admin+)
    Route::middleware('role:admin,super')->prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::patch('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
    });

    // Positions (Admin+)
    Route::middleware('role:admin,super')->prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('index');
        Route::get('/create', [PositionController::class, 'create'])->name('create');
        Route::post('/', [PositionController::class, 'store'])->name('store');
        Route::get('/{position}/edit', [PositionController::class, 'edit'])->name('edit');
        Route::patch('/{position}', [PositionController::class, 'update'])->name('update');
        Route::delete('/{position}', [PositionController::class, 'destroy'])->name('destroy');
    });

    // Org Chart (Admin+)
    Route::middleware('role:admin,super')->prefix('organigrama')->name('org-chart.')->group(function () {
        Route::get('/', [OrgChartController::class, 'index'])->name('index');
    });

    // Regulations (All users can view, Admin+ can manage)
    Route::prefix('regulations')->name('regulations.')->group(function () {
        Route::get('/', [RegulationController::class, 'index'])->name('index');
        Route::get('/{regulation}', [RegulationController::class, 'show'])->name('show');

        // Admin/Super only
        Route::middleware('role:admin,super')->group(function () {
            Route::post('/', [RegulationController::class, 'store'])->name('store');
            Route::patch('/{regulation}', [RegulationController::class, 'update'])->name('update');
            Route::delete('/{regulation}', [RegulationController::class, 'destroy'])->name('destroy');
            Route::post('/{regulation}/toggle', [RegulationController::class, 'toggleStatus'])->name('toggle');
        });
    });

    // Vacations (Admin+)
    Route::middleware('role:admin,super')->prefix('vacations')->name('vacations.')->group(function () {
        Route::get('/', [VacationController::class, 'index'])->name('index');
        Route::get('/create', [VacationController::class, 'create'])->name('create');
        Route::post('/', [VacationController::class, 'store'])->name('store');
        Route::get('/employee/{employee}', [VacationController::class, 'show'])->name('show');
        Route::get('/{vacation}/edit', [VacationController::class, 'edit'])->name('edit');
        Route::patch('/{vacation}', [VacationController::class, 'update'])->name('update');
        Route::delete('/{vacation}', [VacationController::class, 'destroy'])->name('destroy');
    });
});
