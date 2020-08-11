<?php

namespace App\Controllers;

use DateTime;
use DateTimeZone;

use App\Models\{
    Event,
    Session,
    Result,
    Driver,
    Team,
    Track,
    Penalty
};

use App\Controllers\{
    Controller,
    SessionController
};

use App\Transformers\{
    SessionTransformer,
    PenaltyTransformer
};

use League\Fractal\{
    Resource\Item,
    Resource\Collection
};

/**
 * GlobalController
 */
class GlobalController extends Controller
{
    public static function recalculatePositions($session_id) {
        $results = Result::where("session_id", $session_id)->where('result_status', '<', 4)->orderBy('laps', 'DESC')->orderBy('race_time', 'ASC')->get();
        $position = 1;

        foreach ($results as $result) {
            $result->position = $position;
            $result->save();

            $position += 1;
        }
        $results = Result::where("session_id", $session_id)->where('result_status', '>', 4)->orderBy('laps', 'DESC')->orderBy('race_time', 'DESC')->get();
        foreach ($results as $result) {
            $result->position = $position;
            $result->save();

            $position += 1;
        }
        $results = Result::where("session_id", $session_id)->where('result_status', '=', 4)->orderBy('laps', 'DESC')->orderBy('race_time', 'DESC')->get();
        foreach ($results as $result) {
            $result->position = $position;
            $result->save();

            $position += 1;
        }
    }

    public static function recalculateStandings($season_id = 2) {

    }
}
