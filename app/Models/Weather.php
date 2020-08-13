<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Session;


/**
 * Weather Modell
 */
class Weather extends Model
{
    protected $table = 'weather';

    protected $fillable = [
        'id',
        'type',
        'session_id',
        'lap',
        'air_temp',
        'track_temp'
    ];

    public function session() {
        return $this->belongsTo(Session::class);
    }
}