<?php

namespace App\Transformers;

use App\Models\CarNumber;
use League\Fractal\TransformerAbstract;

/**
 * LaptimeTransformer
 */
class LaptimeTransformer extends TransformerAbstract
{
    public function transform (CarNumber $carNumber) {
        return [
            'id' => $carNumber->id,
            'driver_id' => $carNumber->driver_id
        ];
    }
}