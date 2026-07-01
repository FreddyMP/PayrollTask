<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class OrgChartController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $departments = Department::where('company_id', $companyId)
            ->where('is_active', true)
            ->with([
                'employees' => fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('status', 'active')),
                'employees.user',
                'employees.position',
                'positions' => fn ($query) => $query->where('is_active', true)->orderBy('title'),
            ])
            ->withCount([
                'employees as employees_count' => fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('status', 'active')),
                'positions as positions_count' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('name')
            ->get();

        $activeIds = $departments->pluck('id');

        $rootDepartments = $departments->filter(function (Department $department) use ($activeIds) {
            return $department->parent_department_id === null
                || !$activeIds->contains($department->parent_department_id);
        })->values();

        $stats = [
            'departments' => $departments->count(),
            'employees' => $departments->sum('employees_count'),
            'positions' => $departments->sum('positions_count'),
            'levels' => $this->maxDepth($departments, $rootDepartments),
        ];

        return view('org-chart.index', compact('departments', 'rootDepartments', 'stats'));
    }

    private function maxDepth(Collection $departments, Collection $roots, int $depth = 1): int
    {
        if ($roots->isEmpty()) {
            return 0;
        }

        $maxChildDepth = 0;

        foreach ($roots as $root) {
            $children = $departments->where('parent_department_id', $root->id)->values();
            $maxChildDepth = max($maxChildDepth, $this->maxDepth($departments, $children, $depth + 1));
        }

        return max($depth, $maxChildDepth);
    }
}
