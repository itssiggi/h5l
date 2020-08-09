<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\{
    Driver,
    Session,
    Team,
    Pitstop,
    Laptime
};

/**
 * Result Modell
 */
class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'session_id',
        'driver_id',
        'position',
        'result_status',
        'fastest_lap',
        'grid',
        'penalties',
        'pitstops',
        'best_lap_time',
        'race_time',
        'youtube'
    ];

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function getPitstopsAttribute () {
        return Pitstop::where('session_id', $this->session_id)->where('driver_id', $this->driver_id)->get();
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function team() {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function getGapAttribute() {
        return $this->session->winner->race_time - $this->race_time;
    }

    public function getSessionLapsAttribute() {
        return $this->session->laps;
    }

    public function getPointsAttribute() {
        $fastest_lap = $this->fastest_lap;

        $points = [
            0 => [0],
            1 => [25,18,15,12,10,8,6,4,2,1],
            2 => [10,8,6,5,4,3,2,1]
        ];

        $points = $points[$this->session->point_system];

        if ($this->position < count($points) + 1) {
            return $points[$this->position - 1] + (int)$fastest_lap;
        } else {
            return 0;
        }
    }

    public function getLaptimesAttribute() {
        return Laptime::where('session_id', $this->session_id)->where('driver_id', $this->driver_id)->get();
    }

    public function getStintsAttribute() {
        $stints = array();
        if ($this->session->isRace) {
            $tyres = $this->session->tyres;
            $laptimes = $this->laptimes;
            $lastLap = $this->laps;
            
            foreach ($this->pitstops as $pitstop) {
                if (empty($stints)) {
                    $temp = [
                        "tyre" => $tyres[$pitstop->tyre_entry],
                        "begin" => 0,
                        "end" => $pitstop->lap,
                        "averageTime" => $this->getAverageLaptime($laptimes, 0, $pitstop->lap, $pitstop->driver_id),
                        "pitstopTime" => $pitstop->pitstop_time,
                        "tyre_exit" => $tyres[$pitstop->tyre_exit]
                    ];
                } else {
                    $lastPit = (end($stints));
                    $temp = [
                        "tyre" => $tyres[$pitstop->tyre_entry],
                        "begin" => $lastPit["end"] + 1,
                        "end" => $pitstop->lap,
                        "averageTime" => $this->getAverageLaptime($laptimes, $lastPit["end"] + 1, $pitstop->lap, $pitstop->driver_id),
                        "pitstopTime" => $pitstop->pitstop_time,
                        "tyre_exit" => $tyres[$pitstop->tyre_exit]
                    ];
                }
                array_push($stints, $temp); 
            }

            if (!empty($stints)) {
                $temp = [];
                $lastStint = (end($stints));

                if (!($lastStint["end"] >= $lastLap)) {
                    $temp = [
                        "tyre" => $lastStint["tyre_exit"],
                        "begin" => $lastStint["end"] + 1,
                        "end" => $lastLap,
                        "averageTime" => $this->getAverageLaptime($laptimes, $lastStint["end"] + 1, $lastLap, $this->driver_id),
                        "pitstopTime" => null
                    ];
                }
            } else {
                foreach ($laptimes as $laptime) {
                    if ($laptime->driver_id == $this->driver_id and $laptime->lap == 1) {
                        $firstTyre = $laptime->tyre;
                    }
                }

                $temp = [
                    "tyre" => $tyres[$firstTyre],
                    "begin" => 0,
                    "end" => $lastLap,
                    "averageTime" => $this->getAverageLaptime($laptimes, 0, $lastLap, $this->driver_id),
                    "pitstopTime" => 0
                ];

            }

            if (!empty($temp)) {
                array_push($stints, $temp); 
            }
        }
        return $stints;
    }

    public function getAverageLaptime($laptimes, $start, $end, $driver_id) {
        $laptimesInZone = [];

        foreach ($laptimes as $laptime) {
            if ($laptime->driver_id == $driver_id) {
                if ($laptime->lap > $start && $laptime->lap <= $end) {
                    array_push($laptimesInZone, $laptime);
                } elseif ($start == $end or ($start == 0 and $end == 1)) {
                    array_push($laptimesInZone, $laptime);
                }
            }
        }

        $totalTime = 0;
        foreach ($laptimesInZone as $laptime) {
            $totalTime += $laptime->time;
        }
        if (sizeof($laptimesInZone)) {
            $averageLaptime = $totalTime / sizeof($laptimesInZone);
            $mins = floor($averageLaptime / 60 % 60);
            $secs = floor($averageLaptime % 60);
            $milliSecs = (int)((($averageLaptime) - floor($averageLaptime)) * 1000);
            return sprintf('%1d:%02d.%03d', $mins, $secs, $milliSecs);
        }
        return "keine Zeiten";
    }
}