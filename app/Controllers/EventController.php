<?php

namespace App\Controllers;

use App\Models\{
    Event,
    Result,
    Session,
    Setup,
    Standing,
    SafetyCarPhase
};

use App\Controllers\Controller;
use League\Fractal\{
    Resource\Item,
    Resource\Collection
};
use App\Transformers\{
    EventTransformer,
    StandingTransformer,
    ResultTransformer,
    SessionTransformer
};

/**
 * EventController
 */
class EventController extends Controller
{
    public function index($requst, $response) {
        $events = Event::all()->orderBy("planned_start", "DESC");

        $eventsTransformer = new Collection($events, new EventTransformer);

        $data = [
            "events" => $this->c->fractal->createData($eventsTransformer)->toArray()["data"]
        ];

        return $this->c->view->render($response, 'events/index.twig', $data);
    }

    public function show($requst, $response, $args) {
        $event = Event::find($args['id']);

        if ($event === null) {
            return $response->withStatus(500);
        } 

        $sessions = $event->sessions;

        if ($sessions->count() == 3) {
            $standings = $event->standings;

            foreach ($sessions as $session) {
                if ($session->isRace) {
                    if ($session->isMainRace) {
                        $mainRaceResultsTransformer = new Collection($session->results, new ResultTransformer);
                        $mainRaceTransformer = new Item($session, new SessionTransformer);
                    } elseif ($session->isSprintRace) {
                        $sprintRaceResultsTransformer = new Collection($session->results, new ResultTransformer);
                        $sprintRaceTransformer = new Item($session, new SessionTransformer);
                    }
                } elseif ($session->isQuali) {
                    $qualiResultsTransformer = new Collection($session->results, new ResultTransformer);
                    $qualiTransformer = new Item($session, new SessionTransformer);
                }
            }

            $eventTransformer = new Item($event, new EventTransformer);
            $standingsTransformer = new Collection($standings, new StandingTransformer);
            
            $data = [
                "event" => $this->c->fractal->createData($eventTransformer)->toArray()["data"],
                "sessions" => [
                    "quali" => $this->c->fractal->createData($qualiTransformer)->toArray()["data"],
                    "mainRace" => $this->c->fractal->createData($mainRaceTransformer)->toArray()["data"],
                    "sprintRace" => $this->c->fractal->createData($sprintRaceTransformer)->toArray()["data"],
                ],
                "results" => [
                    "quali" => $this->c->fractal->createData($qualiResultsTransformer)->toArray()["data"],
                    "mainRace" => $this->c->fractal->createData($mainRaceResultsTransformer)->toArray()["data"],
                    "sprintRace" => $this->c->fractal->createData($sprintRaceResultsTransformer)->toArray()["data"]
                ],
                "standings" => $this->c->fractal->createData($standingsTransformer)->toArray()["data"]
            ];

            return $this->c->view->render($response, 'events/show_race_race_quali.twig', $data);
        } elseif ($sessions->count() == 2) {
            $standings = $event->standings;

            foreach ($sessions as $session) {
                if ($session->isRace) {
                    $raceResultsTransformer = new Collection($session->results, new ResultTransformer);
                    $race = $this->c->fractal->createData($raceResultsTransformer)->toArray()["data"];
                } elseif ($session->isQuali) {
                    $qualiResultsTransformer = new Collection($session->results, new ResultTransformer);
                    $quali = $this->c->fractal->createData($qualiResultsTransformer)->toArray()["data"];
                }
            }

            $eventTransformer = new Item($event, new EventTransformer);
            $standingsTransformer = new Collection($standings, new StandingTransformer);
            $sessionTransformer = new Collection($sessions, new SessionTransformer);

            if ($qualiResultsTransformer) {
                
            }
            
            
            $data = [
                "event" => $this->c->fractal->createData($eventTransformer)->toArray()["data"],
                "sessions" => $this->c->fractal->createData($sessionTransformer)->toArray()["data"],
                "results" => [
                    "quali" => $quali,
                    "race" => $race
                ],
                "standings" => $this->c->fractal->createData($standingsTransformer)->toArray()["data"]
            ];

            return $this->c->view->render($response, 'events/show_race_quali.twig', $data);
        }
         else {
            return $this->c->view->render($response, 'events/show_no_results.twig', compact("event"));
        }
        
    }

    public function add($request, $response) {
        $event = new Event;

        $event->season_id = $request->getParam("season_id");
        $event->circuit_id = $request->getParam("circuit_id");
        $event->start_time = $request->getParam("start_time");
        $event->prev_event_id = $request->getParam("prev_event_id");
        $event->next_event_id = $request->getParam("next_event_id");

        $event->save();

        return $response->withJson($event);
    }

    public function showSetup($request, $response, $args) {
        $setup = Setup::find($args["id"]);

        $data = [
            "aero" => [
                "name" => "Aerodynamik",
                "Frontflügel" => [
                    "value" => $setup->frontWing,
                    "maxVal" => 11,
                    "minVal" => 1,
                    "max" => strval($setup->frontWing) . " / 11"
                ],
                "Heckflügel" => [
                    "value" => $setup->rearWing,
                    "maxVal" => 11,
                    "minVal" => 1,
                    "max" => strval($setup->rearWing) . " / 11"
                ]
            ],
            "transmission" => [
                "name" => "Transmission",
                "Differential mit Gas" => [
                    "value" => $setup->onThrottle,
                    "maxVal" => 100,
                    "minVal" => 50,
                    "max" => strval($setup->onThrottle) . "% / 100%"
                ],
                "Differential ohne Gas" => [
                    "value" => $setup->offThrottle,
                    "maxVal" => 100,
                    "minVal" => 50,
                    "max" => strval($setup->offThrottle) . "% / 100%"
                ]
            ],
            "suspension" => [
                "name" => "Aufhängung",
                "Front Camber" => [
                    "value" => $setup->frontCamber,
                    "maxVal" => -2.50,
                    "minVal" => -3.50,
                    "max" => strval($setup->frontCamber) . "° / -2.50°"
                ],
                "Rear Camber" => [
                    "value" => $setup->rearCamber,
                    "maxVal" => -1,
                    "minVal" => -2,
                    "max" => strval($setup->rearCamber) . "° / -1.00°"
                ],
                "Front Toe" => [
                    "value" => $setup->frontToe,
                    "maxVal" => 0.15,
                    "minVal" => 0.05,
                    "max" => strval($setup->frontToe) . "° / 0.15°"
                ],
                "Rear Toe" => [
                    "value" => $setup->rearToe,
                    "maxVal" => 0.5,
                    "minVal" => 0.2,
                    "max" => strval($setup->rearToe) . "° / 0.50°"
                ]
            ],
            "brakes" => [
                "name" => "Bremsen",
                "Bremsdruck" => [
                    "value" => $setup->brakePressure,
                    "maxVal" => 100,
                    "minVal" => 50,
                    "max" => strval($setup->brakePressure) . "% / 100%"
                ],
                "Vorderradbremse" => [
                    "value" => $setup->brakeBias,
                    "maxVal" => 70,
                    "minVal" => 50,
                    "max" => strval($setup->brakeBias) . "% / 70%"
                ]
            ],
        ];

        return $this->c->view->render($response, 'setups/show.twig', compact("data"));
    }
}