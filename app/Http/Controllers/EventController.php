<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;

use App\{
    Event,
};

class EventController extends Controller
{
    public function index(Request $request) {
        $inOneWeek = (new DateTime('NOW'))->modify('+1 week')->format('Y-m-d');
        $events = Event::where('planned_start', '<', $inOneWeek)->get()->sortByDesc("planned_start");

        return view('events.index', compact("events"));
    }
}
