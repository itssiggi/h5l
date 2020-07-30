<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Driver;
use App\Models\Session;


/**
 * Laptime Modell
 */
class Laptime extends Model
{
    protected $table = 'lap_times';

    protected $fillable = [
        'id',
        'driver_id',
        'session_id',
        'lap',
        'tyre',
        'valid',
        'time'
    ];

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function session() {
        return $this->belongsTo(Session::class);
    }
}