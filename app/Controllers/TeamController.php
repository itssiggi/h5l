<?php

namespace App\Controllers;

use App\Models\Team;
use App\Controllers\Controller;
use League\Fractal\{
    Resource\Item,
    Resource\Collection
};
use App\Transformers\TeamTransformer;

/**
 * TeamController
 */
class TeamController extends Controller
{
    public function index($requst, $response) {
        $teams = Team::all();

        $transformer = new Collection($teams, new TeamTransformer);

        return $response->withJson($this->c->fractal->createData($transformer)->toArray());
    }

    public function show($requst, $response, $args) {
        $team = Team::where('name', $args['name'])->first();

        if ($team === null) {
            return $response->withStatus(500);
        }

        $transformer = new Item($team, new TeamTransformer);

        return $response->withJson($this->c->fractal->createData($transformer)->toArray());
    }

    public function add($request, $response) {
        $team = new Team;

        $team->name = $request->getParam("name");
        $team->color = $request->getParam("color");

        $team->save();

        return $response->withJson($team);
    }
}