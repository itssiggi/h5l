<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ {
    Standing
};

/**
 * 
 */
class StandingTransformer extends TransformerAbstract
{

    public function transform (Standing $standing) {
        return [
            'id' => $standing->id,
            'position' => $standing->position,
            'wins' => $standing->wins,
            'points' => $standing->points,
            'driver' => $standing->driver,
            'team' => $standing->driver->team,
            'event' => $standing->event
        ];
    }

}