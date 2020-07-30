<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Team;

/**
 * 
 */
class TeamTransformer extends TransformerAbstract
{
    public function transform (Team $team) {
        return [
            'id' => $team->id,
            'name' => $team->name,
            'color' => $team->color,
            'drivers' => $team->drivers
        ];
    }
}