<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\{
    Team,
    Event,
    Result,
    Season
};

/**
 * Driver Modell
 */
class Driver extends Model
{
    protected $table = 'drivers';

    protected $fillable = [
        'name',
        'team_id',
        'short_name'
    ];

    public function getPointsAttribute() {
        $season = Season::latest()->first();
        $standings = Standing::join('events', 'standings.event_id', '=', 'events.id')->where('driver_id', $this->id)->orderBy('events.planned_start', 'Desc')->first();

        return $standings->points;
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function results() {
        return $this->hasMany(Result::class)->orderBy('session_id', 'DESC');
    }

    public function scopeFromSession($query, $session_id) {
        return $query->whereHas('results', function($query) use ($session_id) {
            return $query->whereHas('session', function($query2) use ($session_id) {
                $query2->where('id', $session_id);
            });
        });
    }

    public function getPositionAttribute() {
        $season = Season::latest()->first();
        $standings = Standing::join('events', 'standings.event_id', '=', 'events.id')->where('driver_id', $this->id)->orderBy('events.planned_start', 'Desc')->first();

        return $standings->position;
    }

    public function getEventParticipationsAttribute() {
        $events = Event::fromDriver($this->id)->fromSeason(Season::current()->id)->Official()->get();
        return $events->count();
    }

    public function getPointsPerEventAttribute() {
        return round($this->points / $this->eventParticipations, 1);
    }
}