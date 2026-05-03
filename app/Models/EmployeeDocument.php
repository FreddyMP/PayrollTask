<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = ['employee_id', 'company_id', 'name', 'file_path'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
