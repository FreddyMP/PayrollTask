<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyField extends Model
{
    protected $fillable = ['company_id', 'document_template_id', 'name', 'value', 'is_bold'];

    protected $casts = [
        'is_bold' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }
}
