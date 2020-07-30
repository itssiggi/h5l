<?php

namespace App\Controllers;

use App\Models\Track;
use App\Controllers\Controller;
use League\Fractal\{
    Resource\Item,
    Resource\Collection
};
use App\Transformers\TrackTransformer;

/**
 * TrackController
 */
class TrackController extends Controller
{
    public function index($requst, $response) {
        $tracks = Track::all();

        $transformer = new Collection($tracks, new TrackTransformer);

        return $response->withJson($this->c->fractal->createData($transformer)->toArray());
    }

    public function show($requst, $response, $args) {
        $track = Track::find($args["id"])->first();

        if ($track === null) {
            return $response->withStatus(404);
        }

        $transformer = new Item($track, new TeamTransformer);

        return $response->withJson($this->c->fractal->createData($transformer)->toArray());
    }

    public function add($request, $response) {
        $track = new Track;

        $track->name = $request->getParam("name");
        $track->country = $request->getParam("country");

        $track->save();

        return $response->withJson($track);
    }

    public function update($request, $response, $args) {
        $track = Track::find($args['id']);

        foreach (array_keys($request->getParams()) as $param) {
            $track[$param] = $request->getParam($param);
        }

        if ($track->isDirty()) {
            $track->save();
        }

        return $response->withJson($track);
    }
}