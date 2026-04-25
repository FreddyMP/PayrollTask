<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $fillable = ['company_id', 'title', 'content', 'category', 'file_path'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
