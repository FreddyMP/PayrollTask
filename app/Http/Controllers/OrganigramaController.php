<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganigramaController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Load all departments with nested data
        $departments = Department::where('company_id', $companyId)
            ->with([
                'positions' => function ($q) {
                    $q->with([
                        'employees' => function ($eq) {
                            $eq->with('user')->where(function ($u) {
                                $u->whereHas('user', fn($uq) => $uq->where('status', 'active'));
                            });
                        }
                    ])->where('is_active', true)->orderBy('title');
                },
                'childDepartments.positions.employees.user',
                'parentDepartment',
            ])
            ->whereNull('parent_department_id')
            ->orderBy('name')
            ->get();

        // Stats
        $totalDepartments = Department::where('company_id', $companyId)->where('is_active', true)->count();
        $totalPositions    = Position::where('company_id', $companyId)->where('is_active', true)->count();
        $totalEmployees    = Employee::where('company_id', $companyId)
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->count();
        $unassigned        = Employee::where('company_id', $companyId)
            ->whereNull('department_id')
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->with('user')
            ->get();

        return view('organigrama.index', compact(
            'departments',
            'totalDepartments',
            'totalPositions',
            'totalEmployees',
            'unassigned'
        ));
    }

    public function apiData()
    {
        $companyId = Auth::user()->company_id;

        $departments = Department::where('company_id', $companyId)
            ->where('is_active', true)
            ->with([
                'positions' => function ($q) {
                    $q->where('is_active', true)
                      ->with(['employees.user'])
                      ->orderBy('title');
                },
                'childDepartments' => function ($q) {
                    $q->where('is_active', true)
                      ->with([
                          'positions' => function ($pq) {
                              $pq->where('is_active', true)
                                 ->with(['employees.user'])
                                 ->orderBy('title');
                          }
                      ]);
                }
            ])
            ->whereNull('parent_department_id')
            ->orderBy('name')
            ->get();

        return response()->json($departments);
    }
}
