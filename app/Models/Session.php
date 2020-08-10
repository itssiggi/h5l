<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\{
    Track,
    Pitstop,
    Event,
    Laptime,
    Driver,
    SafetyCarPhase  
};

/**
 * Session Modell
 */
class Session extends Model
{
    protected $table = 'sessions';

    protected $fillable = [
        'weather',
        'track_id',
        'start',
        'end',
        'type',
        'track_temp',
        'air_temp',
        'formula',
        'event_id',
        'session_duration'
    ];

    public function track() {
        return $this->belongsTo(Track::class);
    }

    public function drivers() {
        return $this->hasMany(Driver::class);
    }

    public function event() {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function pitstops() {
        return $this->hasMany(Pitstop::class)->orderBy('lap', 'ASC');
    }

    public function laptimes() {
        return $this->hasMany(Laptime::class);
    }

    public function results() {
        return $this->hasMany(Result::class)->orderBy('position', 'ASC');
    }

    public function getWinnerAttribute() {
        foreach ($this->results as $result) {
            if ($result->position == 1) {
                return $result;
            }
        }
        return False;
    }

    public function getIsRaceAttribute() {
        return $this->type == 10 or $this->type == 11;
    }

    public function getIsMainRaceAttribute() {
        return $this->isRace && $this->main_race;
    }

    public function getIsSprintRaceAttribute() {
        return $this->isRace && $this->sprint_race;
    }
    public function getIsQualiAttribute() {
        return $this->type == 8 or $this->type == 9;
    }

    public function getTyresAttribute() {
        $tyres = array(
            $this->event->track->tyre_soft => 0,
            $this->event->track->tyre_medium => 1,
            $this->event->track->tyre_hard => 2,
            7 => 7,
            8 => 8
        );

        return $tyres;
    }

    public function getPhasesAttribute() {
        if ($this->isRace) {
            return SafetyCarPhase::where('session_id', $this->id)->get();
        }
        return [];
    }

    public function getGridAttribute() {
        $results = Result::where('session_id', $this->id)->orderBy('grid', 'ASC')->get();
        return $results;
    }

    public function getStatisticsAttribute()
    {

        $most_pitstops = [];
        $most_penalties = [];

        $curr_FL = 999999999999;
        $curr_MPIT = -1;
        $curr_MPEN = -1;

        foreach ($this->results as $result) {
            if (sizeof($result->pitstops) > $curr_MPIT) {
                $most_pitstops = [];
                $most_pitstops["driver"] = $result->driver->name;
                $most_pitstops["value"] = sizeof($result->pitstops);
                $curr_MPIT = sizeof($result->pitstops);
            }
            if ($result->fastest_lap) {
                $fastest_lap = [];
                $fastest_lap["driver"] = $result->driver->name;

                $mins = floor($result->best_lap_time / 60 % 60);
                $secs = floor($result->best_lap_time % 60);
                $milliSecs = (int)((($result->best_lap_time) - floor($result->best_lap_time)) * 1000);
                $fastest_lap["value"] = sprintf('%1d:%02d.%03d', $mins, $secs, $milliSecs);
            }
            if ($result->penalties > $curr_MPEN) {
                $curr_MPEN = $result->penalties;
                $most_penalties = [];
                $most_penalties["driver"] = $result->driver->name;
                $most_penalties["value"] = $result->penalties;
            }
        }

        return [
            "fastest_lap" => $fastest_lap,
            "most_pitstops" => $most_pitstops,
            "most_penalties" => $most_penalties
        ];
        
    }
}