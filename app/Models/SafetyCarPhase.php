<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Session;


/**
 * SafetyCarPhase Modell
 */
class SafetyCarPhase extends Model
{
    protected $table = 'safety_cars';

    protected $fillable = [
        'begin',
        'end',
        'virtualSC'
    ];

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

    public function scopeFromSession($query, $session_id) {
        return $query->where('session_id', $session_id);
    }
}