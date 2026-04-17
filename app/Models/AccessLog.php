<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = [
        'user_id', 'login_at', 'logout_at', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAttendanceStatusAttribute()
    {
        $employee = $this->user->employee ?? null;
        if (!$employee || !$employee->work_start || !$employee->work_end) {
            return 'Puntual'; // Default or N/A
        }

        $loginTime = $this->login_at->format('H:i:s');
        $workStart = $employee->work_start;
        $workEnd = $employee->work_end;

        // Standardize work times to H:i:s for comparison
        $workStartFull = \Carbon\Carbon::parse($workStart)->format('H:i:s');
        $workEndFull = \Carbon\Carbon::parse($workEnd)->format('H:i:s');
        
        // Grace period: 15 minutes
        $graceTime = \Carbon\Carbon::parse($workStart)->addMinutes(15)->format('H:i:s');

        if ($loginTime > $workEndFull) {
            return 'Fuera de Horario';
        }

        if ($loginTime > $graceTime) {
            return 'Tarde';
        }

        return 'Puntual';
    }

    public function getDeviceNameAttribute()
    {
        $device = Device::where('company_id', $this->user->company_id ?? null)
            ->where('ip_address', $this->ip_address)
            ->first();
            
        return $device ? $device->name : $this->ip_address;
    }
}
