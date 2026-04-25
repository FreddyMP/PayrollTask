<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentStep extends Model
{
    protected $fillable = ['vacancy_id', 'name', 'responsible_id', 'points', 'sort_order'];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function progress()
    {
        return $this->hasMany(CandidateProgress::class);
    }
}
