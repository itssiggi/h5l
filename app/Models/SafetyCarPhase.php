<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Session;


/**
 * SafetyCarPhase Modell
 */
class SafetyCarPhase extends Model
{
    protected $table = 'safety_cars';

    protected $fillable = [
        'begin',
        'end',
        'virtualSC'
    ];

    public function session() {
        return $this->belongsTo(Session::class);
    }
}