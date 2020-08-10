<?php

namespace App\Transformers;

use App\Models\Penalty;
use League\Fractal\TransformerAbstract;

/**
 * PenaltyTransformer
 */
class PenaltyTransformer extends TransformerAbstract
{
    public function transform (Penalty $penalty) {
        return [
            'id' => $penalty->id,
            'driver_id' => $penalty->driver_id,
            'driver' => $penalty->driver,
            'session_id' => $penalty->session_id,
            'lap' => $penalty->lap,
            'penaltyString' => $penalty->penaltyString,
            'infringementString' => $penalty->infringementString,
            'other_driver_id' => $penalty->other_driver_id,
            'time' => $penalty->time,
            'reverted' => $penalty->reverted
        ];
    }
}