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
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
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
