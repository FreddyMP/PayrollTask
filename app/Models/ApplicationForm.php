<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationForm extends Model
{
    protected $fillable = ['company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fields()
    {
        return $this->hasMany(ApplicationFormField::class)->orderBy('sort_order');
    }
}
