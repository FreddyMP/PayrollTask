<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEventLink extends Model
{
    protected $fillable = [
        'calendar_event_id', 'url', 'label',
    ];

    public function event()
    {
        return $this->belongsTo(CalendarEvent::class, 'calendar_event_id');
    }
}
