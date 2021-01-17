<?php

namespace App\Controllers;

use App\Models\{
    Event,
    Session,
    Result,
    Driver,
    Team,
    Track,
    Penalty,
    Season
};

use App\Transformers\{
    SessionTransformer,
    PenaltyTransformer,
    DriverTransformer
};

use App\Controllers\{
    Controller,
    GlobalController
};

use App\Controllers\SessionController;
use League\Fractal\{
    Resource\Item,
    Resource\Collection
};

/**
 * SessionElementController
 */
class SessionElementController extends Controller
{

}
