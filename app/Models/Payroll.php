<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id', 'company_id', 'period', 'gross_salary', 'extras', 'descuentos',
        'ars', 'afp', 'isr', 'deductions', 'net_salary', 'payment_date', 'receipt_path', 'status',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'gross_salary' => 'decimal:2',
            'extras' => 'decimal:2',
            'descuentos' => 'decimal:2',
            'ars' => 'decimal:2',
            'afp' => 'decimal:2',
            'isr' => 'decimal:2',
            'deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
