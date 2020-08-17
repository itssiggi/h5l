<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Driver;
use App\Models\Session;


/**
 * Pitstop Modell
 */
class Pitstop extends Model
{
    protected $table = 'pitstops';

    protected $fillable = [
        'id',
        'driver_id',
        'session_id',
        'tyre_entry',
        'tyre_exit',
        'pitting_time',
        'pitstop_time',
        'lap'
    ];

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function session() {
        return $this->belongsTo(Session::class);
    }

    public function scopeFromEvent($query, $event_id) {
        return $query->whereHas('session', function($query) use ($event_id) {
            return $query->whereHas('event', function($query2) use ($event_id) {
                $query2->where('id', $event_id);
            });
        });
    }
}