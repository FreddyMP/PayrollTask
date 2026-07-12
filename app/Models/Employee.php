<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'department', 'department_id', 'position_id', 'salary',
        'hire_date', 'contract_type', 'work_start', 'work_end', 'bank_account', 'id_number',
        'break_start', 'break_end', 'role',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department_rel()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function fichajes()
    {
        return $this->hasMany(Fichaje::class);
    }

    public function arsExtras()
    {
        return $this->hasMany(EmployeeArsExtra::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function vacations()
    {
        return $this->hasMany(Vacation::class);
    }

    public function getTotalArsExtraAttribute()
    {
        return $this->arsExtras()->sum('ars_amount');
    }

    /**
     * Calcula los años de antigüedad del empleado
     */
    public function getYearsOfServiceAttribute(): float
    {
        if (!$this->hire_date) {
            return 0;
        }
        return $this->hire_date->diffInYears(now());
    }

    /**
     * Obtiene los días de vacaciones que le corresponden según antigüedad
     */
    public function getVacationDaysEntitledAttribute(): int
    {
        $yearsOfService = $this->years_of_service;

        // Empleados con menos de 1 año no tienen vacaciones
        if ($yearsOfService < 1) {
            return 0;
        }

        // Menos de 5 años: 14 días
        // 5 años o más: 18 días
        return $yearsOfService >= 5 ? 18 : 14;
    }

    /**
     * Obtiene los días de vacaciones tomados en un año específico
     */
    public function getVacationDaysTaken(int $year = null): int
    {
        $year = $year ?? now()->year;

        if (!$this->user) {
            return 0;
        }

        $requests = $this->user->requests()
            ->where('type', 'vacation')
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->get();

        $days = 0;
        foreach ($requests as $request) {
            if ($request->start_date && $request->end_date) {
                $days += \App\Models\Vacation::calculateBusinessDays(
                    \Carbon\Carbon::parse($request->start_date),
                    \Carbon\Carbon::parse($request->end_date),
                    $this->company_id
                );
            }
        }

        return $days;
    }

    /**
     * Obtiene los días de vacaciones restantes en un año específico
     */
    public function getVacationDaysRemaining(int $year = null): int
    {
        $entitled = $this->vacation_days_entitled;
        $taken = $this->getVacationDaysTaken($year);

        return max(0, $entitled - $taken);
    }
}
