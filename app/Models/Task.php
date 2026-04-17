<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'company_id', 'project_id', 'title', 'description', 'status',
        'priority', 'assigned_to', 'created_by', 'due_date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class , 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}
