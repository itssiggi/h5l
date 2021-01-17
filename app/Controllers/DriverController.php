<?php

namespace App\Controllers;

use App\Models\{
    Driver,
    Result,
    Session,
    Standing,
    Event
};
use App\Controllers\Controller;
use League\Fractal\{
    Resource\Item,
    Resource\Collection
};
use App\Transformers\{
    DriverTransformer,
    ResultTransformer,
    TeamTransformer,
    SessionTransformer,
    EventTransformer
};

use \Illuminate\Database\QueryException;

/**
 * DriverController
 */
class DriverController extends Controller
{
    public function index($requst, $response, $api = false) {
        $drivers = Driver::all();

        $transformer = new Collection($drivers, new DriverTransformer);

        if ($api) {
            return $response->withJson($this->c->fractal->createData($transformer)->toArray()["data"]);
        }

        return $response->withJson($this->c->fractal->createData($transformer)->toArray());
    }

    public function apiCreateDriver($request, $response) {
        $driver = new Driver;

        $driver->name = $request->getParam("name");
        $driver->team_id = $request->getParam("team_id");
        $driver->short_name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->getParam("name")), '-'));

        try {
            $driver->save();
        } catch (QueryException $ex) {
            return $response->withStatus(400);
        }

        return $response
            ->withJson($driver)
            ->withStatus(201);
    }

    public function apiGetDriver($request, $response, $args) {
        if (is_numeric($args['id'])) {
            $driver = Driver::find($args['id']);
        } else {
            $driver = Driver::where('name', $args['id'])->first();
        }

        if ($driver === null) {
            return $response->withStatus(404);
        } else {
            $transformer = new Item($driver, new DriverTransformer());
            $driver = $this->c->fractal
                ->parseIncludes((!is_null($request->getParam('include'))) ? $request->getParam('include') : [])
                ->createData($transformer)
                ->toArray()["data"];

            return $response
                ->withJson($driver)
                ->withStatus(200);
        }   
    }

    public function apiGetDrivers($request, $response) {
        $drivers = Driver::all();

        if ($drivers === null) {
            return $response->withStatus(404);
        } else {
            return $response
                ->withJson($drivers)
                ->withStatus(200);
        }   
    }

    public function apiDeleteDriver($request, $response) {
        if (is_numeric($request->getParam("id"))) {
            $driver = Driver::find($request->getParam("id"));
        } else {
            $driver = Driver::where('name', $request->getParam("id"))->first();
        }

        if ($driver === null) {
            return $response->withStatus(500);
        } else {
            $driver->delete();
            return $response->withStatus(204);
        }   
    }

    public function apiUpdateDriver($request, $response, $args) {
        $driver = Driver::find($args['id']);

        if ($driver === null) {
            return $response->withStatus(400);
        } else {
            $driver->name = (string) $request->getParam("name");
            $driver->team_id = (int) $request->getParam("team_id");
            $driver->short_name = (string) strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $driver->name), '-'));
        }

        $driver->save;
        return $response
            ->withJson($driver)
            ->withStatus(200);
    }

    public function show($requst, $response, $args) {
        $driver = Driver::where('short_name', $args['name'])->first();
        $events = Event::fromDriver($driver->id)->orderBy('planned_start', 'DESC')->get();

        if ($driver === null) {
            return $response->withStatus(500);
        }

        $results = Result::fromDriver($driver->id)->isRace()->isOfficial()->get()->sortByDesc(function($query){
               return $query->session->start;
            });

        $driverTransformer = new Item($driver, new DriverTransformer);
        $eventTransformer = new Collection($events, new EventTransformer);

        if (!$results->isEmpty()) {
            $resultTransformer = new Collection($results, new ResultTransformer);
            $teamTransformer = new Item($driver->team, new TeamTransformer);
            $data = [
                "driver" => $this->c->fractal->createData($driverTransformer)->toArray()["data"],
                "team" => $this->c->fractal->createData($teamTransformer)->toArray()["data"],
                "results" => $this->c->fractal->createData($resultTransformer)->toArray()["data"],
                "events" => $this->c->fractal->createData($eventTransformer)->toArray()["data"]
            ];
        } else {
            $data = [
                "driver" => $this->c->fractal->createData($driverTransformer)->toArray()["data"]
            ];
        }

        return $this->c->view->render($response, 'drivers/show.twig', $data);
    }

    public function update($request, $response, $args) {
        $driver = Driver::where('name', $args['name'])->first();

        foreach (array_keys($request->getParams()) as $param) {
            $driver[$param] = $request->getParam($param);
        }

        if ($driver->isDirty()) {
            $driver->save();
        }

        return $response->withJson($driver);
    }

    public function destroy($request, $response, $args) {
        $driver = Driver::where('name', $args['name'])->first();

        if ($driver === null) {
            return $response->withStatus(500);
        } else {
            $driver->delete();
            return $response->withStatus(204);
        }   
    }

    public function calcPoints($position, $point_system, $fastest_lap) {
        $points = [
            [25, 18, 15, 12, 10, 8, 6, 4, 2, 1],
            [10, 6, 4, 3, 2, 1]
        ];

        $points = $points[$point_system - 1];

        if ($position < sizeof($points) + 1) {
            return $points[$position - 1] + (int)$fastest_lap;
        }
        else {
            return 0;
        }
    }
}
