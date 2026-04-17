<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestAttachment extends Model
{
    protected $fillable = [
        'user_request_id', 'user_id', 'file_path', 'file_type',
    ];

    public function userRequest()
    {
        return $this->belongsTo(UserRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
