<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    protected $table = 'requests';

    protected $fillable = [
        'user_id', 'company_id', 'type', 'status',
        'start_date', 'end_date', 'description',
        'admin_notes', 'reviewed_by', 'reviewed_at',
        'overtime_date', 'overtime_start', 'overtime_end',
        'overtime_hours', 'approved_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'overtime_date' => 'date',
            'reviewed_at' => 'datetime',
            'overtime_hours' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class , 'reviewed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function attachments()
    {
        return $this->hasMany(RequestAttachment::class);
    }
}
