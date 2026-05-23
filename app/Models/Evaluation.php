<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['company_id', 'title', 'description', 'allow_multiple_responses', 'status'];

    protected $casts = [
        'allow_multiple_responses' => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function questions() { return $this->hasMany(EvaluationQuestion::class)->orderBy('order'); }
    public function assignments() { return $this->hasMany(EvaluationAssignment::class); }
    public function responses() { return $this->hasMany(EvaluationResponse::class); }
}
