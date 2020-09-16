<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Driver;
use App\Models\Event;

/**
 * Standing Modell
 */
class Standing extends Model
{
    protected $table = 'standings';

    protected $fillable = [
        'event_id',
        'driver_id',
        'season_id',
        'points',
        'wins'
    ];

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function event() {
        return $this->belongsTo(Event::class);
    }

    public function scopeFromEvent($query, $event_id) {
        return $query->where('event_id', $event_id);
    }

    public function scopeToEvent($query, $event_id) {
        $event = Event::find($event_id);
        return $query->whereHas('event', function($query) use ($event) {
            $query->where('planned_start', '<=', $event->planned_start);
        });
    }

    public function scopeFromSeason($query, $season_id) {
        return $query->where('season_id', $season_id);
    }

}