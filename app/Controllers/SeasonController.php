<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};
use League\Fractal\{
    Resource\Item,
    Resource\Collection
};

use DateTime;

use App\Transformers\{
    RaceTransformer,
    StandingTransformer,
    DriverTransformer,
    TeamTransformer
};


use App\Models\ {
    Season,
    Result,
    Event,
    Driver,
    Session,
    Standing,
    Team
};

class SeasonController extends Controller
{
    public function show(Request $request, Response $response, $args)
    {
        $season = Season::where('year', $args['year'])->get()->first();
        $racer = $season->bestRacer($season);
        # $racer = $season->bestQualifier($season);

        return $response->withJson($racer);
    }

    public function apiCurrentStandings($request, $response, $args) {
        return $this->currentStandings($request, $response, $args, $api = true);
    }

    public function currentStandings($request, $response, $args, $api = Null)
    {
        $currentSeason = Season::orderBy('id', 'DESC')->first();
        $events = Event::orderBy('planned_start', 'DESC')->where('planned_start', '<', new DateTime(date()))->where('season_id', $currentSeason->id)->where('regular_event', 1)->get();
        foreach ($events as $event) {
            $standings = Standing::where('event_id', $event->id)->orderBy('points', 'DESC')->get();
            if ($standings) {
                break;
            }
        }

        $teams = Team::limit(10)->get();
        $teams = $teams->sortByDesc(function ($team) {
            return $team->points;
        });
        $teamTransformer = new Collection($teams, new TeamTransformer);
        $teams = $this->c->fractal->createData($teamTransformer)->toArray()["data"];

        $transformer = new Collection($standings, new StandingTransformer);
        $standings = $this->c->fractal->createData($transformer)->toArray()["data"];

        if ($api) {
            return $response->withJson($standings);
        }

        return $this->c->view->render($response, 'standings/index.twig', compact("standings", "teams"));
    }

    public function getRules($request, $response, $args) {
        return $this->c->view->render($response, 'other/rules.twig');
    }

    public function standings(Request $request, Response $response, $args) {
        $season = Season::where('year', $args['year'])->get()->first();

        if (isset($args["race_id"]) && isset($season)) {
            $prev = $season->first_race_id;
            $transactions = [];
            $racesAmount = Race::where('season_id', $season->id)->count();
            $transactions = [];
            $race = null;
            for ($i=0; $i < $racesAmount; $i++) { 
                if (!$race) {
                    $race = Race::find($season->first_race_id);
                } else {
                    $race = Race::where('prev_race_id', $race->id)->first();
                }
                $transaction = Transaction::where('race_id', $race->id)->get();
                foreach ($transaction as $t) {
                    array_push($transactions, $t);
                }
                if ($race->id == $args["race_id"]) {
                    break;
                }
            }
        } else {
            $transactions = Transaction::where('season_id', $season->id)->get();
        }
        $drivers = [];

        foreach ($transactions as $transaction) {
            if (empty($drivers[$transaction->driver_id])) {
                $drivers[$transaction->driver_id] = 0;
            }
            $drivers[$transaction->driver_id] += $transaction->points_added;
        }

        foreach ($drivers as $key => $value) {
            $driver = Driver::find($key);
            $driver->points = $value;
            $drivers[$key] = $driver;
        }

        $points = array();
        foreach ($drivers as $key => $row)
        {
            $points[$key] = $row['points'];
        }
        array_multisort($points, SORT_DESC, $drivers);

        $transformer = new Collection($drivers, new DriverTransformer);

        return $response->withJson($this->c->fractal->createData($transformer)->toArray());
    }
}
