<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeArsExtra extends Model
{
    protected $fillable = [
        'employee_id', 'name', 'id_number', 'relationship', 'birth_date', 'sex', 'phone', 'address', 'ars_amount'
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'ars_amount' => 'decimal:2',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
