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
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'standings',
        'team',
        'carNumber'
    ];

    public function transform(Driver $driver)
    {
        return [
            'id' => (int) $driver->id,
            'name' => (string) $driver->name,
            'uses_steering_wheel' => (boolean) $driver->uses_steering_wheel,
        ];
    }

    public function includeCarNumber(Driver $driver) {
        $carNumber = $driver->carNumber;

        return $this->item($carNumber, new CarNumberTransformer);
    }

    public function includeStandings(Driver $driver) {
        $standing = $driver->standings;

        return $this->collection($standing, new StandingTransformer);
    }

    public function includeTeam(Driver $driver) {
        $team = $driver->team;

        return $this->item($team, new TeamTransformer);
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