<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\SafetyCarPhase;

/**
 * SafetyCarPhaseTransformer
 */
class SafetyCarPhaseTransformer extends TransformerAbstract
{
    public function transform (SafetyCarPhase $safetyCarPhase) {
        return [
            'id' => $safetyCarPhase->id,
            'start' => $safetyCarPhase->start,
            'end' => $safetyCarPhase->end,
            'isVirtual' => boolval($safetyCarPhase->virtualSC),
            'session_id' => $safetyCarPhase->session_id
        ];
    }
}