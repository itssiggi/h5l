<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DateTime;

use App\Models\{
    Session,
    Driver,
    Result,
    Season,
    Track,
    Standing
};

/**
 * Event Modell
 */
class Event extends Model
{
    protected $table = 'events';

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'name',
        'planned_start',
        'season_id',
        'regular_event',
        'point_system',
        'track_id'
    ];

    public function season() {
        return $this->belongsTo(Season::class);
    }

    public function sessions() {
        return $this->hasMany(Session::class);
    }

    public function track() {
        return $this->belongsTo(Track::class);
    }

    public function standings() {
        return $this->hasMany(Standing::class)->orderBy('points', 'DESC');
    }

    public function results()
    {
        return $this->hasManyThrough(Result::class, Session::class);
    }

    public function scopeOfficial($query) {
        return $query->where('regular_event', 1);
    }

    public function scopeFromDriver($query, $driver_id) {
        return $query->whereHas('sessions', function($query) use ($driver_id) {
            return $query->whereHas('results', function($query2) use ($driver_id) {
                $query2->where('driver_id', $driver_id);
            });
        });
    }

    public function scopeFromSeason($query, $season_id) {
        return $query->where('season_id', $season_id);
    }

    public function scopePast($query) {
        return $query->where('planned_start', '<',  new DateTime());
    }

    public function scopeFuture($query) {
        return $query->where('planned_start', '>',  new DateTime());
    }

    public function scopePastInOneWeek($query) {
        return $query->where('planned_start', '<',  (new DateTime())->modify("+1 week"));
    }

    public function scopeCurrentSeason($query) {
        $season = Season::orderBy('id', 'DESC')->first();
        return $query->where('season_id', $season->id);
    }

    public function getMainRaceAttribute() {
        $session = Session::fromEvent($this->id)->mainRace()->first();
        if ($session) {
            return $session;
        }
        return 0;
    }

    public function getSprintRaceAttribute() {
        $session = Session::fromEvent($this->id)->sprintRace()->first();
        if ($session) {
            return $session;
        }
        return 0;
    }

    public function getTyresAttribute() {
        return array(
            $this->track->tyre_soft => 0,
            $this->track->tyre_medium => 1,
            $this->track->tyre_hard => 2,
            7 => 7,
            8 => 8
        );
    }

    public function getRaceWeatherAttribute() {
        $session = Session::fromEvent($this->id)->mainRace()->first();
        if ($session) {
            return $session->weather;
        }
        return 0;
    }

    public function getStatisticsAttribute()
    {
        $session = Session::fromEvent($this->id)->mainRace()->first();
        if ($session) {
            return $session->statistics;
        }

        return [
            "fastest_lap" => Null,
            "most_pitstops" => Null,
            "most_penalties" => Null
        ];
        
    }
}