<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Weather;

/**
 * Weather Transformer
 */
class WeatherTransformer extends TransformerAbstract
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
        'session'
    ];

    public function transform (Weather $weather) {
        return [
            'type' => $weather->type,
            'air_temp' => $weather->air_temp,
            'track_temp' => $weather->track_temp,
            'lap' => $weather->lap
        ];
    }

    public function includeSession (Weather $weather) {
        $session = $weather->session;

        return $this->item($session, new SessionTransformer);
    }
}