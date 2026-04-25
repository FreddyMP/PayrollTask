<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = ['vacancy_id', 'name', 'email', 'phone', 'cv_path', 'status'];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function progress()
    {
        return $this->hasMany(CandidateProgress::class);
    }

    public function getTotalPointsAttribute()
    {
        return $this->progress()->sum('score');
    }

    public function getCurrentStepAttribute()
    {
        // Get the first incomplete step
        $vacancySteps = $this->vacancy->steps;
        $completedStepIds = $this->progress()->where('status', 'completed')->pluck('recruitment_step_id')->toArray();

        foreach ($vacancySteps as $step) {
            if (!in_array($step->id, $completedStepIds)) {
                return $step;
            }
        }

        return null; // All steps completed
    }
}
