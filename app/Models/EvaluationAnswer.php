<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationAnswer extends Model
{
    protected $fillable = ['evaluation_response_id', 'evaluation_question_id', 'answer_text', 'answer_scale', 'answer_boolean'];

    protected $casts = [
        'answer_boolean' => 'boolean',
    ];

    public function response() { return $this->belongsTo(EvaluationResponse::class, 'evaluation_response_id'); }
    public function question() { return $this->belongsTo(EvaluationQuestion::class, 'evaluation_question_id'); }
}
