<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Driver;


/**
 * CarNumber Modell
 */
class CarNumber extends Model
{
    protected $table = 'carnumbers';

    protected $fillable = [
        'id',
        'driver_id'
    ];

    public function driver() {
        return $this->belongsTo(Driver::class);
    }
}