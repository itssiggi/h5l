<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Track Modell
 */
class Track extends Model
{
    protected $table = 'tracks';

    protected $hidden = [
        'created_at',
        'updated_at',
        'tyre_soft',
        'tyre_medium',
        'tyre_hard'
    ];


    protected $fillable = [
        'name',
        'country'
    ];

    public function getTyresAttribute() {
        $tyres = array(
            $this->tyre_soft => 0,
            $this->tyre_medium => 1,
            $this->tyre_hard => 2,
            7 => 7,
            8 => 8
        );

        return $tyres;
    }
}