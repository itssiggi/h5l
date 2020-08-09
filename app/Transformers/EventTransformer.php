<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Event;
use App\Models\Result;

/**
 * Event Transformer
 */
class EventTransformer extends TransformerAbstract
{
    public function transform (Event $event) {
        return [
            'id' => $event->id,
            'name' => $event->name,
            'planned_start' => $event->planned_start,
            'regular_event' => $event->regular_event,
            'season' => $event->season,
            'youtube' => $event->youtube,
            'track' => $event->track,
            'statistics' => $event->statistics,
            'weather' => $event->raceWeather
        ];
    }
}