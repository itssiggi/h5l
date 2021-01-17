<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Session;

/**
 * Session Transformer
 */
class SessionTransformer extends TransformerAbstract
{
   /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //'team',
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'weather',
        'track',
        'drivers',
        'phases',
        'event'
    ];

    public function transform (Session $session) {
        return [
            'id' => $session->id,
            'start' => $session->start,
            'end' => $session->end,
            'event_id' => $session->event_id,
            'mainRace' => $session->main_race,
            'sprintRace' => $session->sprint_race,
            'type' => $session->type,
            'track_temp' => $session->track_temp,
            'air_temp' => $session->air_temp,
            'formula' => $session->air_temp,
            // 'statistics' => $session->statistics,
            // 'hasDetails' => $session->hasDetails,
            'laps' => $session->laps
        ];
    }

    public function includeTrack (Session $session) {
        $track = $session->track;

        return $this->item($track, new TrackTransformer);
    }

    public function includeWeather (Session $session) {
        $weather = $session->weatherData;

        return $this->collection($weather, new WeatherTransformer);
    }

    public function includePhases (Session $session) {
        $sc_phases = $session->phases;

        return $this->collection($sc_phases, new SafetyCarPhaseTransformer);
    }

    public function includeLaptimes (Session $session) {
        $laptimes = $session->laptimes;

        return $this->collection($laptimes, new LaptimeTransformer);
    }

    public function includeResults (Session $session) {
        $results = $session->results;

        return $this->collection($results, new ResultTransfomer);
    }

}