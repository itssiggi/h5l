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

    public function getRealTyreAttribute() {
        $tyres = $this->session->track->tyres;
        return $tyres[$this->tyre];
    }

    public function getTimeAsStringAttribute() {
        $mins = abs(intval(floor($this->time / 60 % 60)));
        $secs = abs(intval(floor($this->time % 60)));
        $milliSecs = abs((int)((($this->time) - floor($this->time)) * 1000));
        $string = sprintf('0.%03d', $milliSecs);

        if ($secs and $this->time) {
            $string = sprintf('%01d.%03d', $secs, $milliSecs);
        }
        if ($mins and $this->time) {
            $string = sprintf('%1d:%02d.%03d', $mins, $secs, $milliSecs);
        }

        return $string;
    }
}