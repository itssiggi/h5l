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
 * AdminController
 */
class AdminController extends Controller
{
    public function getIndex($request, $response) {
        return $this->c->view->render($response, 'admin/index.twig', compact(""));
    }

    public function getAddEvent($request, $response) {
        $tracks = Track::all();

        return $this->c->view->render($response, 'admin/addEvent.twig', compact("tracks"));
    }

    public function getRecalculateStandings($request, $response) {
        GlobalController::recalculateStandings();
        $this->c->flash->addMessage('success', 'WM Stand erfolgreich neu berechnet.');
        return $response->withRedirect($this->c->router->pathFor('admin.index'));
    }

    public function getEditPenalties($request, $response, $args) {
        $session = Session::find($args["session_id"]);
        $penalties = Penalty::where('session_id', $session->id)->orderBy('driver_id', 'ASC')->get();
        $drivers = Driver::fromSession($session->id)->get();

        $sessionTransformer = new Item($session, new SessionTransformer);
        $session = $this->c->fractal->createData($sessionTransformer)->toArray()["data"];

        $penaltyTransformer = new Collection($penalties, new PenaltyTransformer);
        $penalties = $this->c->fractal->createData($penaltyTransformer)->toArray()["data"];

        $driverTransformer = new Collection($drivers, new DriverTransformer);
        $drivers = $this->c->fractal->createData($driverTransformer)->toArray()["data"];

        return $this->c->view->render($response, 'admin/editPenalties.twig', compact("penalties", "session", "drivers"));
    }

    public function postAddPenalty($request, $response, $args) {
        # Find Session
        $session = Session::find($args["session_id"]);
        
        # Find Driver
        $driver = Driver::find($request->getParam('driver_id'));

        # Find Result
        $result = Result::fromDriver($driver->id)->fromSession($session->id)->first();

        if ($session != null and $result != null) {

            # Add Penalty to DB
            $penalty = new Penalty;
            $penalty->driver_id = $driver->id;
            $penalty->lap = $request->getParam('lap');
            $penalty->time = $request->getParam('time');
            $penalty->infringement_type = $request->getParam('infringement_id');
            $penalty->penalty_type = $request->getParam('penalty_id');
            $penalty->session_id = $session->id;
            $penalty->stewards = true;
            $penalty->save();

            # Update result of driver
            $result->penalties += $penalty->time;
            $result->save();

            # Recalculate positions in session
            GlobalController::recalculatePositions($penalty->session_id);

            # Recalculate standings
            GlobalController::recalculateStandings();

            $this->c->flash->addMessage('success', 'Strafe für ' . $driver->name . ' hinzugefügt.');
        } else {
            $this->c->flash->addMessage('error', 'Session nicht gefunden.');
        }
        return $response->withRedirect($this->c->router->pathFor('admin.editPenalties', ["session_id" => $args["session_id"]]));
    }

    public function validatePenalty($request, $response, $args) {
        $penalty = Penalty::find($args["id"]);
        $penalty->reverted = 0;
        $penalty->save();
        $result = Result::where("session_id", $penalty->session_id)->where("driver_id", $penalty->driver_id)->first();
        $result->penalties += $penalty->time;
        $result->save();

        GlobalController::recalculatePositions($penalty->session_id);
        GlobalController::recalculateStandings();

        return $response->withRedirect($this->c->router->pathFor('admin.editPenalties', ["session_id" => $penalty->session_id]));
    }

    public function invalidatePenalty($request, $response, $args) {
        $penalty = Penalty::find($args["id"]);
        $penalty->reverted = 1;
        $penalty->save();
        $result = Result::where("session_id", $penalty->session_id)->where("driver_id", $penalty->driver_id)->first();
        $result->penalties -= $penalty->time;
        $result->save();

        GlobalController::recalculatePositions($penalty->session_id);
        GlobalController::recalculateStandings();

        return $response->withRedirect($this->c->router->pathFor('admin.editPenalties', ["session_id" => $penalty->session_id]));
    }

    public function postAddEvent($request, $response) {
        $planned_start = new DateTime(
            $request->getParam('planned_start_date') 
            . 'T' . 
            $request->getParam('planned_start_time'));
        $planned_start->setTimezone(new DateTimeZone('Europe/Berlin'));

        
        $event = new Event;
        $event->track_id = $request->getParam("track_id");
        $event->regular_event = $request->getParam("regular_event");
        $event->planned_start = $planned_start;
        $event->name = $request->getParam("name");
        $event->season_id = $request->getParam("season_id");

        $event->save();

        return $this->c->view->render($response, 'admin/addEvent.twig');
    }

    public function getEditEventResult ($request, $response, $args) {
        $event =  Event::find($args["event_id"]);
        $results = Result::join('sessions', 'sessions.session_id', '=', 'results.session_id')->where('sessions.type', 10)->where('sessions.event_id', $event->id)->orderBy('position', 'asc')->select("results.id", "results.driver_id", "results.team_id", "results.position")->get();
        $drivers = Driver::all();
        $teams = Team::all();

        return $this->c->view->render($response, 'admin/editEventResult.twig', compact("event", "results", "drivers", "teams"));
    }

    public function postEditEventResult ($request, $response, $args) {
        foreach ($request->getParams() as $key => $value) {
            $paramParts = explode("_", $key);
            if ($paramParts[1] == "driver") {
                $raceResult = Result::find($paramParts[0]);
                $qualiResultID = Result::join('sessions', 'sessions.session_id', '=', 'results.session_id')->where('sessions.event_id', $args['event_id'])->where("sessions.type", 8)->where("results.driver_id", $raceResult->driver_id)->select("results.id")->first();

                
                
                $driver = Driver::find($value);
                $raceResult->driver_id = $driver->id;

                if ($qualiResultID) {
                    $qualiResult = Result::find((int)$qualiResultID->id);
                    $qualiResult->driver_id = $driver->id;
                    $qualiResult->save();
                }
            
                # $raceResult->team_id = $driver->team_id;
                # $qualiResult->team_id = $driver->team_id;
                $raceResult->save();
            }
        }

        return $response->withRedirect($this->c->router->pathFor('admin.editEventResult', ["event_id" => $args["event_id"]]));
    }

    public function getAddEventwithResults ($request, $response, $args) {
        $drivers = Driver::all();
        $teams = Team::limit(10)->get();
        $tracks = Track::all();
        $events = Event::all();

        return $this->c->view->render($response, 'admin/addEventwithResults.twig', compact("events", "drivers", "teams", "tracks"));
    }

    public function postAddEventwithResults ($request, $response) {
        $event = Event::find($request->getParam("event_id"));
        $track = Track::find($event->track_id);

        $datetime = new DateTime();
        $timezone = new DateTimeZone('Europe/Berlin');
        $datetime->setTimezone($timezone);

        $qualiStart = strtotime($event->planned_start);
        $qualiEnd = $qualiStart + 18*60 + rand(0,240);
        $raceStart = $qualiEnd + 4*60 + rand(0,240);
        $raceEnd = $raceStart + 44*60 + rand(0,480);

        $raceSession = new Session();
        $raceSession->weather = rand(0, 3);
        $raceSession->track_id = $event->track_id;
        $raceSession->start = $datetime->setTimestamp($raceStart);
        $raceSession->end = $datetime->setTimestamp($raceEnd);
        $raceSession->type = 10;
        $raceSession->laps = $request->getParam('laps');
        $raceSession->main_race = true;
        $raceSession->point_system = 1;
        $raceSession->track_temp = rand(25, 35);
        $raceSession->air_temp = rand(19, 27);
        $raceSession->formula = 0;
        $raceSession->event_id = $event->id;
        $raceSession->session_duration = $raceEnd - $raceStart;

        $raceSession->save();
        

        $raceResults = [];
        $qualiResults = [];
        $racePosition = 0;
        $qualiPosition = 0;
        $baseRaceTime == 0;

        foreach ($request->getParams() as $key => $value) {
            $resultElement = explode("_", $key);
            if ($resultElement[0] == "rennen") {
                $raceResults[$resultElement[1]][$resultElement[2]] = $value;
            }
            elseif ($resultElement[0] == "quali") {
                $qualiResults[$resultElement[1]][$resultElement[2]] = $value;
            }
        }

        foreach ($raceResults as $position => $resultDriver) {
            if ($resultDriver["driverId"] != 0) {
                $driver = Driver::find($resultDriver["driverId"]);
                $result = new Result();

                $result->session_id = $raceSession->id;
                $result->driver_id = $driver->id;
                $result->team_id = $resultDriver["teamId"];
                $result->position = $position;
                $result->result_status = 3;
                if ($resultDriver["dnf"]) {
                    $result->result_status = 6;
                }
                $result->grid = $resultDriver["grid"];
                $result->penalties = $resultDriver["penalties"];
                $result->pitstops = $resultDriver["pitstops"];
                $result->laps = $raceSession->laps;
                $result->best_lap_time = $this->timeToFloatBestLap($resultDriver);
                if ($baseRaceTime == 0) {
                    $baseRaceTime = $this->timeToFloatRaceTime($resultDriver);
                    $result->race_time = $baseRaceTime - $result->penalties;
                } else {
                    if ($this->timeToFloatRaceTime($resultDriver) > 0) {
                        $result->race_time = $baseRaceTime + $this->timeToFloatRaceTime($resultDriver) - $result->penalties;
                    } else {
                        $result->race_time = 0;
                    }       
                }
                $result->save();
            }
        }

        $qualiSession = new Session();
        $qualiSession->weather = rand(0, 3);
        $qualiSession->track_id = $event->track_id;
        $qualiSession->start = $datetime->setTimestamp($qualiStart);
        $qualiSession->end = $datetime->setTimestamp($qualiEnd);
        $qualiSession->type = 8;
        $qualiSession->point_system = 0;
        $qualiSession->laps = 99;
        $qualiSession->track_temp = rand(25, 35);
        $qualiSession->air_temp = rand(19, 27);
        $qualiSession->formula = 0;
        $qualiSession->event_id = $event->id;
        $qualiSession->session_duration = $qualiEnd - $qualiStart;

        $qualiSession->save();

        foreach ($qualiResults as $position => $resultDriver) {
            if ($resultDriver["driverId"] != 0) {
                $driver = Driver::find($resultDriver["driverId"]);
                $result = new Result();

                $result->session_id = $qualiSession->id;
                $result->driver_id = $driver->id;
                $result->team_id = $resultDriver["teamId"];
                $result->position = $position;
                $result->result_status = 3;
                if ($resultDriver["dnf"]) {
                    $result->result_status = 6;
                }
                $result->grid = 0;
                $result->penalties = 0;
                $result->pitstops = 0;
                $result->laps = $qualiSession->laps;
                $result->best_lap_time = $this->timeToFloatBestLap($resultDriver);
                $result->race_time = $this->timeToFloatRaceTime($resultDriver);
                $result->save();
            }
        }

        return $this->c->view->render($response, 'admin/addEventwithResults.twig');
    }

    public function getQualiResultByDriverId($quali_result, $driver_id) {
        foreach ($quali_result as $key => $value) {
            if ($value["driverId"] == $driver_id) {
                return $key;
            }
        }
        return 0;
    }

    public function timeToFloatBestLap ($object) {
        return (float)((int)$object["bestLapTimeMin"]*60 + (int)$object["bestLapTimeSec"] + (float)($object["bestLapTimeMilli"])/1000);
    }

    public function timeToFloatRaceTime ($object) {
        return (float)((int)$object["raceTimeMin"]*60 + (int)$object["raceTimeSec"] + (float)($object["raceTimeMilli"])/1000);
    }

}
