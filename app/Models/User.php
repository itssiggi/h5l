<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * User Modell
 */
class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'id',
        'name',
        'password',
        'discord'
    ];

    public function setPoints($points) {
        $this->points = $points;
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }
}