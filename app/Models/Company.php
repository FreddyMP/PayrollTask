<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'rnc', 'email', 'phone', 'address', 'logo', 'plan', 'status',
        'saturday_rest', 'sunday_rest',
    ];

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function requests()
    {
        return $this->hasMany(UserRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
