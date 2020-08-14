<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Team;
use App\Models\Result;

/**
 * Driver Modell
 */
class Driver extends Model
{
    protected $table = 'drivers';
    protected $points = null;

    protected $fillable = [
        'name',
        'team_id',
        'short_name'
    ];

    public function getPointsAttribute() {
        $season = Season::latest()->first();
        $standings = Standing::join('events', 'standings.event_id', '=', 'events.id')->where('driver_id', $this->id)->orderBy('events.planned_start', 'Desc')->first();

        return $standings->points;
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function results() {
        return $this->hasMany(Result::class)->orderBy('session_id', 'DESC');
    }

    public function getPositionAttribute() {
        $season = Season::latest()->first();
        $standings = Standing::join('events', 'standings.event_id', '=', 'events.id')->where('driver_id', $this->id)->orderBy('events.planned_start', 'Desc')->first();

        return $standings->position;
    }
}