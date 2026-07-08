<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fichaje extends Model
{
    protected $fillable = [
        'employee_id',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'total_hours',
        'work_mode',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_out_latitude',
        'clock_out_longitude',
    ];

    protected function casts(): array
    {
        return [
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
