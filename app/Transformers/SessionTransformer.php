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
            'event' => $session->event,
            'weather' => $session->weather,
            'mainRace' => $session->main_race,
            'sprintRace' => $session->sprint_race,
            'type' => $session->type,
            'track' => $session->track,
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