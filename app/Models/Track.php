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
        'updated_at'
    ];


    protected $fillable = [
        'name',
        'country',
        'soft_tyre',
        'medium_tyre',
        'hard_tyre'
    ];
}