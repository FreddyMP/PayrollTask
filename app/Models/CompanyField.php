<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyField extends Model
{
    protected $fillable = ['company_id', 'name', 'value', 'is_bold'];

    protected $casts = [
        'is_bold' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
