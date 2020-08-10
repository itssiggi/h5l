<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Driver;
use App\Models\Session;


/**
 * Penalty Modell
 */
class Penalty extends Model
{
    protected $table = 'penalties';

    protected $fillable = [
        'id',
        'driver_id',
        'session_id',
        'lap',
        'infringement_type'
    ];

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function session() {
        return $this->belongsTo(Session::class);
    }

    public function getPenaltyStringAttribute() {
        $penalties = array(
            0 => "Durchfahrtsstrafe",
            1 => "Stop Go",
            2 => "Gridstrafe",
            3 => "Penalty reminder",
            4 => "Zeitstrafe",
            5 => "Warning",
            6 => "Disqualifiziert",
            7 => "Removed from formation lap",
            8 => "Parked too long timer",
            9 => "Tyre regulations",
            10 => "This lap invalidated",
            11 => "This and next lap invalidated",
            12 => "This lap invalidated without reason",
            13 => "This and next lap invalidated without reason",
            14 => "This and previous lap invalidated",
            15 => "This and previous lap invalidated without reason",
            16 => "Ausgeschieden",
            17 => "Black flag timer");
        return $penalties[$this->penalty_type];
    }

    public function getinfringementStringAttribute() {
        $infringements = array(
            0 => "Blocking by slow driving",
            1 => "Blocking by wrong way driving",
            2 => "Reversing off the start line",
            3 => "Schwere Kollision",
            4 => "Leichte Kollision",
            5 => "Collision failed to hand back position single",
            6 => "Collision failed to hand back position multiple",
            7 => "Corner cutting gained time",
            8 => "Corner cutting overtake single",
            9 => "Corner cutting overtake multiple",
            10 => "Crossed pit exit lane",
            11 => "Ignorieren blauer Flaggen",
            12 => "Ignorieren gelber Flaggen",
            13 => "Ignoring drive through",
            14 => "Too many drive throughs",
            15 => "Drive through reminder serve within n laps",
            16 => "Drive through reminder serve this lap",
            17 => "Rasen in der Boxengasse",
            18 => "Parked for too long",
            19 => "Ignoring tyre regulations",
            20 => "Too many penalties",
            21 => "Mehrere Vergehen",
            22 => "Approaching disqualification",
            23 => "Tyre regulations select single",
            24 => "Tyre regulations select multiple",
            25 => "Lap invalidated corner cutting",
            26 => "Lap invalidated running wide",
            27 => "Corner cutting ran wide gained time minor",
            28 => "Corner cutting ran wide gained time significant",
            29 => "Corner cutting ran wide gained time extreme",
            30 => "Lap invalidated wall riding",
            31 => "Lap invalidated flashback used",
            32 => "Lap invalidated reset to track",
            33 => "Blocking the pitlane",
            34 => "Jump start",
            35 => "Safety car to car collision",
            36 => "Safety car illegal overtake",
            37 => "Rasen unter SC",
            38 => "Rasen unter VSC",
            39 => "Formation lap below allowed speed",
            40 => "Retired mechanical failure",
            41 => "Retired terminally damaged",
            42 => "Safety car falling too far back",
            43 => "Black flag timer",
            44 => "Nicht absolvierte Stop Go",
            45 => "Nicht absolvierte Drive-Through",
            46 => "Engine component change",
            47 => "Gearbox change",
            48 => "League grid penalty",
            49 => "Retry penalty",
            50 => "Illegal time gain",
            51 => "Mandatory pitstop"
        );
        return $infringements[$this->infringement_type];
    }

}