<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProgress extends Model
{
    protected $fillable = ['candidate_id', 'recruitment_step_id', 'score', 'status', 'notes', 'completed_at', 'scheduled_at'];

    protected $casts = [
        'completed_at' => 'datetime',
        'scheduled_at' => 'date',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function step()
    {
        return $this->belongsTo(RecruitmentStep::class, 'recruitment_step_id');
    }
}
