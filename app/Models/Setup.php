<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Result;


/**
 * Setup Modell
 */
class Setup extends Model
{
    protected $table = 'setups';

    protected $fillable = [
        'id',
        'frontWing',
        'rearWing',
        'onThrottle',
        'offThrottle',
        'frontCamber',
        'rearCamber',
        'frontToe',
        'rearToe',
        'frontSuspension',
        'rearSuspension',
        'frontAntiRollBar',
        'rearAntiRollBar',
        'frontSuspensionHeight',
        'rearSuspensionHeight',
        'brakePressure',
        'brakeBias',
        'rearLeftTyrePressure',
        'rearRightTyrePressure',
        'frontLeftTyrePressure',
        'frontRightTyrePressure',
        'ballast',
        'fuelLoad'
    ];

}