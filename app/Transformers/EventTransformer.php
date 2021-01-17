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
        'sessions',
        'track',
        'season'
    ];

    public function transform (Event $event) {
        return [
            'id' => $event->id,
            'name' => $event->name,
            'planned_start' => $event->planned_start,
            'regular_event' => $event->regular_event,
            'season_id' => $event->season_id,
            // 'statistics' => $event->statistics,
            // 'weather' => $event->raceWeather
        ];
    }

    public function includeSessions (Event $event) {
        $sessions = $event->sessions;

        if ($event->has('sessions')) {
            return $this->collection($sessions, new SessionTransformer);
        }
    }

    public function includeTrack (Event $event) {
        $track = $event->track;
        
        return $this->item($track, new TrackTransformer);
    }

    public function includeSeason (Event $event) {
        $season = $event->season;
       
        return $this->item($season, new SeasonTransformer);
    }
}