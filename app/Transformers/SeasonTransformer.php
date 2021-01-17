<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

use App\Models\Season;

/**
 * SeasonTransformer
 */
class SeasonTransformer extends TransformerAbstract
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
        'events'
    ];

    public function transform (Season $season) {
        return [
            'id' => (int) $season->id,
            'year' => (int) $season->year
        ];
    }

    public function includeEvents (Season $season) {
        $events = $season->events;

        return $this->collection($events, new EventTransformer);
    }

}