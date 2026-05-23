<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationAssignment extends Model
{
    protected $fillable = ['evaluation_id', 'employee_id', 'is_completed'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function evaluation() { return $this->belongsTo(Evaluation::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}
