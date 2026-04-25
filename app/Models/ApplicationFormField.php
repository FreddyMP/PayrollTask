<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationFormField extends Model
{
    protected $fillable = ['application_form_id', 'label', 'type', 'options', 'sort_order'];

    protected $casts = [
        'options' => 'array',
    ];

    public function applicationForm()
    {
        return $this->belongsTo(ApplicationForm::class);
    }
}
