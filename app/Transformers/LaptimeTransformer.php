<?php

namespace App\Transformers;

use App\Models\Laptime;
use League\Fractal\TransformerAbstract;

/**
 * LaptimeTransformer
 */
class LaptimeTransformer extends TransformerAbstract
{
    public function transform (Laptime $laptime) {
        return [
            'id' => $laptime->id,
            'driver_id' => $laptime->driver_id,
            'sector_one' => $laptime->sector_one,
            'sector_two' => $laptime->sector_two,
            'sector_three' => $laptime->sector_three,
            'time' => [
                'value' => $laptime->time,
                'string' => $laptime->timeAsString
            ],
            'tyre' => $laptime->realTyre
        ];
    }
}