<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Session;


/**
 * Weather Modell
 */
class Weather extends Model
{
    protected $table = 'weather';

    protected $fillable = [
        'id',
        'type',
        'session_id',
        'lap',
        'air_temp',
        'track_temp'
    ];

    public function session() {
        return $this->belongsTo(Session::class);
    }

    public function scopeFromSession($query, $session_id) {
        return $query->where('session_id', $session_id);
    }

    public function scopeFromEvent($query, $event_id) {
        return $query->whereHas('session', function($query) use ($event_id) {
            return $query->whereHas('event', function($query2) use ($event_id) {
                $query2->where('id', $event_id);
            });
        });
    }
}