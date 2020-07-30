<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Session;

/**
 * Session Transformer
 */
class SessionTransformer extends TransformerAbstract
{
    public function transform (Session $session) {
        return [
            'id' => $session->id,
            'start' => $session->start,
            'end' => $session->end,
            'weather' => $session->weather,
            'type' => $session->type,
            'track_temp' => $session->track_temp,
            'air_temp' => $session->air_temp,
            'formula' => $session->air_temp,
            'track' => $session->track,
            'statistics' => $session->statistics,
            'SC_phases' => $session->phases,
            'laps' => $session->laps
        ];
    }
}