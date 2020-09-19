<?php

namespace App\Controllers;

use DateTime;

use App\Models\{
    Event,
    Result,
    Session,
    Setup,
    Standing,
    SafetyCarPhase,
    Weather,
    Penalty,
    Pitstop,
    Laptime
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
        $inOneWeek = (new DateTime('NOW'))->modify('+1 week')->format('Y-m-d');
        $events = Event::where('planned_start', '<', $inOneWeek)->get()->sortByDesc("planned_start");

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

        if ($event->amountRaces == 2) {
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
        } elseif ($event->amountRaces == 1) {
            $standings = $event->standings;

            foreach ($sessions as $session) {
                if ($session->isRace) {
                    if ($session->isMainRace) {
                        $mainRaceResultsTransformer = new Collection($session->results, new ResultTransformer);
                        $mainRaceTransformer = new Item($session, new SessionTransformer);
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
                    "mainRace" => $this->c->fractal->createData($mainRaceTransformer)->toArray()["data"]
                ],
                "results" => [
                    "quali" => $this->c->fractal->createData($qualiResultsTransformer)->toArray()["data"],
                    "mainRace" => $this->c->fractal->createData($mainRaceResultsTransformer)->toArray()["data"]
                ],
                "standings" => $this->c->fractal->createData($standingsTransformer)->toArray()["data"]
            ];

            return $this->c->view->render($response, 'events/show_race_quali.twig', $data);
        } elseif ($event->amountRaces > 2) {

            $sessionData = [];

            foreach ($sessions as $session) {
                if ($session->isRace) {
                    $sessionTransformer = new Item($session, new SessionTransformer);
                    $resultTransformer = new Collection($session->results, new ResultTransformer);

                    array_push($sessionData, 
                        [
                            "info" => $this->c->fractal->createData($sessionTransformer)->toArray()["data"],
                            "results" => $this->c->fractal->createData($resultTransformer)->toArray()["data"]
                        ]
                    );  
                }
            }

            $standings = $event->standings;

            $eventTransformer = new Item($event, new EventTransformer);
            $standingsTransformer = new Collection($standings, new StandingTransformer);

            $data = [
                "event" => $this->c->fractal->createData($eventTransformer)->toArray()["data"],
                "sessions" => $sessionData,
                "standings" => $this->c->fractal->createData($standingsTransformer)->toArray()["data"]
            ];

            return $this->c->view->render($response, 'events/show_multi_races.twig', $data);
        }
         else {
            return $this->c->view->render($response, 'events/show_no_results.twig', compact("event"));
        }
        
    }

    public function deleteEventResults($request, $response, $args) {
        if ($args["id"] > 0) {
            $event = Event::find($args["id"]);

            $penalties = Penalty::fromEvent($event->id)->delete();
            $pitstops = Pitstop::fromEvent($event->id)->delete();
            $sc_phases = SafetyCarPhase::fromEvent($event->id)->delete();
            $lap_times = Laptime::fromEvent($event->id)->delete();
            $weather = Weather::fromEvent($event->id)->delete();
            $results = Result::fromEvent($event->id)->delete();
            $sessions = Session::fromEvent($event->id)->delete();

            GlobalController::recalculateStandings();

            $this->c->flash->addMessage('success', 'Ergebnisse von Event "' . $event->name . '" gelöscht.');

            return $response->withRedirect($this->c->router->pathFor('admin.deleteEventResults.index'));
        } else {
            $events = Event::has('sessions')->get();
            return $this->c->view->render($response, 'admin/deleteEventResults.twig', compact("events"));
        }
        return $response->withRedirect($this->c->router->pathFor('admin.index'));
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