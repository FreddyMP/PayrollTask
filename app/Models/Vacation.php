<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Vacation extends Model
{
    protected $fillable = [
        'employee_id',
        'company_id',
        'start_date',
        'end_date',
        'days_taken',
        'year',
        'notes',
        'status',
        'is_paid',
        'paid_at',
        'payment_payroll_id',
        'payment_amount',
        'payment_ars',
        'payment_afp',
        'payment_isr',
        'payment_net',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'is_paid' => 'boolean',
        'payment_amount' => 'decimal:2',
        'payment_ars' => 'decimal:2',
        'payment_afp' => 'decimal:2',
        'payment_isr' => 'decimal:2',
        'payment_net' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payment_payroll_id');
    }

    public function getCalculatedAmountAttribute(): float
    {
        $salary = $this->employee?->salary ?? 0;
        return round($this->days_taken * ($salary / 23.83), 2);
    }

    public function isEligibleForPayment(): bool
    {
        if ($this->status !== 'approved' || $this->is_paid) {
            return false;
        }

        if (!$this->start_date) {
            return false;
        }

        // Vacaciones con inicio de disfrute máximo a 5 días posteriores a hoy (o ya iniciadas)
        return $this->start_date->lte(now()->addDays(5)->endOfDay());
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calcula días hábiles entre dos fechas
     * Excluye días festivos y días de descanso configurados por la empresa
     */
    public static function calculateBusinessDays(Carbon $startDate, Carbon $endDate, $companyId): int
    {
        $days = 0;
        $currentDate = $startDate->copy();

        // Obtener configuración de la empresa
        $company = \App\Models\Company::find($companyId);
        if (!$company) {
            return 0;
        }

        // Obtener días festivos de la empresa en el rango de fechas
        $holidays = \App\Models\Holiday::where('company_id', $companyId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();

        while ($currentDate->lte($endDate)) {
            $isWeekend = false;

            // Verificar si es sábado y está configurado como descanso
            if ($currentDate->dayOfWeek === Carbon::SATURDAY && $company->saturday_rest) {
                $isWeekend = true;
            }

            // Verificar si es domingo y está configurado como descanso
            if ($currentDate->dayOfWeek === Carbon::SUNDAY && $company->sunday_rest) {
                $isWeekend = true;
            }

            // Verificar si es día festivo
            $isHoliday = in_array($currentDate->format('Y-m-d'), $holidays);

            // Solo contar si no es fin de semana ni festivo
            if (!$isWeekend && !$isHoliday) {
                $days++;
            }

            $currentDate->addDay();
        }

        return $days;
    }

    /**
     * Calcula automáticamente los días antes de guardar
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($vacation) {
            if ($vacation->start_date && $vacation->end_date && $vacation->company_id) {
                $vacation->days_taken = self::calculateBusinessDays(
                    Carbon::parse($vacation->start_date),
                    Carbon::parse($vacation->end_date),
                    $vacation->company_id
                );
                $vacation->year = Carbon::parse($vacation->start_date)->year;
            }
        });
    }
}
