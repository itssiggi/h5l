<?php

namespace App\Controllers;

use App\Models\{
    Session,
    Result,
    Driver,
    Laptime,
    Event,
    SafetyCarPhase
};

use App\Transformers\{
    SessionTransformer,
    ResultTransformer,
    LaptimeTransformer,
    PenaltyTransformer
};

use App\Controllers\Controller;
use League\Fractal\{
    Resource\Item,
    Resource\Collection
};

use \Illuminate\Database\QueryException;

/**
 * SessionController
 */
class SessionController extends Controller
{
    public function show($request, $response, $args) {

        $session = Session::find($args["id"]);
        $drivers = Driver::fromSession($session->id)->orderBy('team_id', 'ASC')->get();
        $scPhases = SafetyCarPhase::fromSession($session->id)->get();

        if ($session) {
            $type = $session->type;
            $results = $session->results;
            $grid = $session->grid;
            $laptimes = $session->laptimes;
            $penalties = $session->penalties->sortBy('lap');

            $laptimesWithoutBox = Laptime::fromSession($session->id)->orderBy('time', 'ASC')->get();

            $groups = $laptimesWithoutBox->split(2);
            $medianLaptime = $groups[1]->first();
            $medianLaptime->time = $medianLaptime->time * 1.08;

            $chartData = array();
            $min = $laptimesWithoutBox->first();
            $max = $laptimesWithoutBox->last();

            foreach ($drivers as $driver) {
                $label = $driver->name;
                $data = array();
                $visible = "legendonly";

                foreach ($laptimes as $laptime) {
                    if ($driver->id == $laptime->driver_id) {
                        array_push($data, floatval($laptime->time));
                        if ($min == $laptime or $laptimes[6] == $laptime) {
                            $visible = true;
                        }
                    }
                }
                $array = array(
                    'label' => $driver->name,
                    'fill' => false,
                    'data' => $data,
                    'visible' => $visible,
                    'borderColor' => $driver->team->color
                );

                array_push($chartData, $array);
            }

            $min->time = $min->time * 0.99;
            $chartInfo = array(
                'min' => $min->timeAsString,
                'max' => $medianLaptime->timeAsString,
                'laps' => $session->laps
            );

            $sessionTransformer = new Item($session, new SessionTransformer);
            $resultTransformer = new Collection($results, new ResultTransformer);
            $laptimeTransformer = new Collection($laptimes, new LaptimeTransformer);
            $penaltyTransformer = new Collection($penalties, new PenaltyTransformer);
            
            $session = $this->c->fractal->createData($sessionTransformer)->toArray()["data"];
            $results = $this->c->fractal->createData($resultTransformer)->toArray()["data"];
            $laptimes = $this->c->fractal->createData($laptimeTransformer)->toArray()["data"];
            $penalties = $this->c->fractal->createData($penaltyTransformer)->toArray()["data"];


            if ($type == 10 or $type == 11) {
                $template = "race";
            } elseif ($type == 9) {
                $template = "OSQ";
            } elseif ($type == 8) {
                $template = "short_quali";
            }
            return $this->c->view->render($response, 'sessions/' . $template . '.twig', compact("session", "results", "grid", "laptimes", "penalties", "chartData", "chartInfo", "scPhases"));
        } else {
            return $response->withRedirect($this->c->router->pathFor('events.index'));
        }
    }

    public function apiCreateSession($request, $response) {
        $session = new Session;

        $session->weather = $request->getParam('weather');
        $session->track_id = $request->getParam('track_id');
        $session->start = $request->getParam('start');
        $session->end = $request->getParam('end');
        $session->type = $request->getParam('type');
        $session->track_temp = $request->getParam('track_temp');
        $session->air_temp = $request->getParam('air_temp');
        $session->formula = $request->getParam('formula');
        $session->event_id = $request->getParam('event_id');
        $session->main_race = $request->getParam('main_race');
        $session->sprint_race = $request->getParam('sprint_race');
        $session->point_system = $request->getParam('point_system');
        $session->laps = $request->getParam('laps');
        $session->session_duration = $request->getParam('session_duration');

        try {
            $session->save();
        } catch (QueryException $ex) {
            return $response->withStatus(400);
        }

        return $response
                ->withJson($session)
                ->withStatus(201);
    }

    public function apiDeleteSession($request, $response) {
        if (is_numeric($request->getParam("id"))) {
            $session = Session::find($request->getParam("id"));
        }

        if ($session === null) {
            return $response->withStatus(500);
        } else {
            $session->delete();
            return $response->withStatus(204);
        }
    }

    public function apiGetSession($request, $response, $args) {
        $session = Session::find($args['id']);

        if ($session === null) {
            return $response->withStatus(404);
        } else {
            $transformer = new Item($session, new SessionTransformer());

            $session = $this->c->fractal
                ->parseIncludes((!is_null($request->getParam('include'))) ? $request->getParam('include') : [])
                ->createData($transformer)
                ->toArray()["data"];

            return $response
                ->withJson($session)
                ->withStatus(200);
        }   
    }

    public function apiGetEventSessions($request, $response, $args) {
        $event = Event::find($args['id']);

        if ($event === null) {
            return $response->withStatus(404);
        }

        $sessions = $event->sessions;

        if ($sessions->count() > 0) {
            return $response
                ->withJson($sessions)
                ->withStatus(200);
        } else {
            return $response->withStatus(204);
        }   
    }

}
