<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Driver;

/**
 * Team Modell
 */
class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'name',
        'color'
    ];

    public function drivers() {
        return $this->hasMany(Driver::class);
    }

    public function getPointsAttribute() {
        $totalPoints = 0;
        foreach ($this->drivers as $driver) {
            $results = $driver->results;
            if (!$results->isEmpty()) {
                foreach ($results as $result) {
                    $totalPoints += $result->points;
                }
            }
        }
        return $totalPoints;
    }

}