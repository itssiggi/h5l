<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Event;
use App\Models\Driver;

/**
 * Season Modell
 */
class Season extends Model
{
    protected $table = 'seasons';

    protected $fillable = [
        'name',
        'year'
    ];

    public function events() {
        return $this->hasMany(Event::class);
    }

    public function scopeCurrent() {
        return $query->orderBy('id', 'DESC');
    }

    public function bestQualifier(Season $season) {
        $drivers = Driver::all();
        $newDrivers = [];

        foreach ($drivers as $driver) {
            $qualifierScore = 0;
            $raceAmounts = 0;
            $results = Result::where('driver_id', $driver->id)->get();
            foreach ($results as $result) {
                if ($result->grid) {
                    $positionsGained = $result->grid;
                    $raceAmounts += 1;
                    $qualifierScore += $positionsGained;
                }
            }
            $qualifierScore = $qualifierScore/$raceAmounts;
            $driver->qualifierScore = $qualifierScore;
            if ($raceAmounts > 4) {
                array_push($newDrivers, $driver);
            }
        }

        usort($newDrivers, array($this, "cmpQualifier"));
        return $newDrivers;
    }

    public function bestRacer(Season $season) {
        $drivers = Driver::all();
        $newDrivers = [];

        foreach ($drivers as $driver) {
            $racingScore = 0;
            $raceAmounts = 0;
            $results = Result::where('driver_id', $driver->id)->get();
            foreach ($results as $result) {
                if ($result->grid) {
                    $positionsGained = $result->grid - $result->position;
                    $raceAmounts += 1;
                    $racingScore += $positionsGained;
                }
            }
            $racingScore = $racingScore/$raceAmounts;
            $driver->racingScore = $racingScore;
            if ($raceAmounts > 4) {
                array_push($newDrivers, $driver);
            }
        }

        usort($newDrivers, array($this, "cmpRacing"));
        return $newDrivers;
    }

    public function cmpRacing($a, $b)
    {
       return ($b->racingScore > $a->racingScore);
    }

    public function cmpQualifier($a, $b)
    {
       return ($b->qualifierScore < $a->qualifierScore);
    }
}