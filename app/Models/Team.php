<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Driver;

/**
 * Team Modell
 */
class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'name',
        'color'
    ];

    public function drivers() {
        return $this->hasMany(Driver::class);
    }

}