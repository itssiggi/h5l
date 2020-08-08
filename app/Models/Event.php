<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\{
    Session,
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

    public function getMainRaceAttribue() {
        $sessions = $this->sessions;
        foreach ($sessions as $session) {
            if ($session->isMainRace) {
                return $session;
            }
        }
        return 0;
    }

    public function getSprintRaceAttribue() {
        $sessions = $this->sessions;
        foreach ($sessions as $session) {
            if ($session->isSprintRace) {
                return $session;
            }
        }
        return 0;
    }

    public function getTyresAttribute() {
        $tyres = array(
            $this->track->tyre_soft => 0,
            $this->track->tyre_medium => 1,
            $this->track->tyre_hard => 2,
            7 => 7,
            8 => 8
        );

        return $tyres;
    }

    public function getRaceWeatherAttribute() {
        $sessions = $this->sessions;
        foreach ($sessions as $session) {
            if ($session->isRace) {
                return $session->weather;
            }
        }
        return 0;
    }

    public function getStatisticsAttribute()
    {
        $sessions = $this->sessions;
        foreach ($sessions as $session) {
            if ($session->isMainRace) {
                return $session->statistics;
            }
        }

        return [
            "fastest_lap" => Null,
            "most_pitstops" => Null,
            "most_penalties" => Null
        ];
        
    }
}