<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $fillable = ['company_id', 'title', 'description', 'department', 'status'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function steps()
    {
        return $this->hasMany(RecruitmentStep::class)->orderBy('sort_order');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
