<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_id', 'name', 'email', 'password',
        'role', 'phone', 'position', 'avatar', 'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class , 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class , 'created_by');
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    public function requests()
    {
        return $this->hasMany(UserRequest::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }

    public function isSuper(): bool
    {
        return $this->role === 'super';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super', 'admin']);
    }

    public function isSupervisor(): bool
    {
        return in_array($this->role, ['super', 'admin', 'supervisor']);
    }

    public function hasMinRole(string $role): bool
    {
        $levels = ['super' => 1, 'admin' => 2, 'supervisor' => 3, 'usuario' => 4];
        return ($levels[$this->role] ?? 99) <= ($levels[$role] ?? 99);
    }

    public function hasRole(string $role): bool
    {
        return $this->hasMinRole($role);
    }
}
