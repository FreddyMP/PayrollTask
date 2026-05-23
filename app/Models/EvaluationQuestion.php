<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationQuestion extends Model
{
    protected $fillable = ['evaluation_id', 'question_text', 'type', 'order', 'is_required'];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function evaluation() { return $this->belongsTo(Evaluation::class); }
    public function answers() { return $this->hasMany(EvaluationAnswer::class); }
}
