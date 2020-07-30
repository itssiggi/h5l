<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ {
    Driver,
    Result,
    CarNumber
};

/**
 * 
 */
class DriverTransformer extends TransformerAbstract
{

    public function transform (Driver $driver) {
        return [
            'id' => $driver->id,
            'name' => $driver->name,
            'team' => $driver->team->name,
            'uses_steering_wheel' => $driver->uses_steering_wheel,
            'points' => $driver->points,
            'position' => $driver->position,
            'carNumber' => (CarNumber::where('driver_id', $driver->id)->first())->id,
            # 'fastest_laps' => $this->calculateFastestLaps($driver)
        ];
    }

    public function calculatePoints(Driver $driver)
    {
        $results = Result::where('driver_id', $driver->id)->get()->sortBy('race.start_time');

        $points = 0;

        foreach ($results as $result)
        {
            $points += Transaction::where('result_id', $result->id)->first()->points_added;
        }

        return $points;
    }

    public function calculateFastestLaps(Driver $driver)
    {
        $results = Result::where('driver_id', $driver->id)->get()->sortBy('race.start_time');

        $fastest_laps = 0;

        foreach ($results as $result)
        {
            if ($result->fastest_lap) {
                $fastest_laps += 1;
            }
        }

        return $fastest_laps;
    }

}