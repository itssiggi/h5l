<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Track;

/**
 * Track Transformer
 */
class TrackTransformer extends TransformerAbstract
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

    ];

    public function transform (Track $track) {
        return [
            'id' => $track->id,
            'name' => $track->name,
            'country' => $track->country,
            'tyre_soft' => $track->tyre_soft,
            'tyre_medium' => $track->tyre_medium,
            'tyre_hard' => $track->tyre_hard
        ];
    }
}