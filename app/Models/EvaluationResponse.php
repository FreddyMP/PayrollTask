<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationResponse extends Model
{
    protected $fillable = ['evaluation_id', 'employee_id'];

    public function evaluation() { return $this->belongsTo(Evaluation::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function answers() { return $this->hasMany(EvaluationAnswer::class); }
}
