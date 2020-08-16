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
    Penalty,
    Standing
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
        $results = Result::where("session_id", $session_id)->where('result_status', '=', 3)->orderBy('laps', 'DESC')->orderBy('race_time', 'ASC')->get();
        $position = 1;
        $results = $results->sortBy(function ($result) {
                    return $result->raceTimeWithPenalties;
                })->sortByDesc('laps');

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
        $events = Event::where('season_id', $season_id)->where('regular_event', 1)->orderBy('planned_start', 'ASC')->get();

        if ($events) {
            # Delete old Standings
            foreach ($events as $event) {
                $standings = Standing::where('event_id', $event->id)->get();
                if ($standings) {
                    foreach ($standings as $standing) {
                        $standing->forceDelete();
                    }
                }
            }

            # Create list of all drivers involved
            $driver_ids = [];
            foreach ($events as $event) {
                $sessions = $event->sessions;
                if ($sessions) {
                    foreach ($sessions as $session) {
                        $results = $session->results;
                        if ($results) {
                            foreach ($results as $result) {
                                $driver_points[$result->driver_id] = 0;
                                $driver_wins[$result->driver_id] = 0;
                            }
                        }
                    }
                }
            }

            # Generate new Standings
            foreach ($events as $event) {
                foreach ($driver_points as $driver_id => $value) {
                    $results = Result::fromDriver($driver_id)->fromEvent($event->id)->isOfficial()->isRace()->get();
                    foreach ($results as $result) {
                        $driver_ids[$driver_id] += $result->points;
                        if ($result->position == 1) {
                            $driver_wins[$driver_id] += 1;
                        }
                    }

                    $standing = new Standing;
                    $standing->event_id = $event->id;
                    $standing->season_id = $season_id;
                    $standing->driver_id = $driver_id;
                    $standing->points = $driver_ids[$driver_id];
                    $standing->wins = $driver_wins[$driver_id];
                    $standing->save();
                }

                // Calculate Positions
                $standings = Standing::fromEvent($event->id)->orderBy('points', 'DESC')->get();
                $position = 1;
                foreach ($standings as $standing) {
                    $standing->position = $position;
                    $position += 1;
                    $standing->save();
                }
            }


        }
    }
}
