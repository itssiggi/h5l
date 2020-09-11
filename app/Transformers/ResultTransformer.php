<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\{
    Result,
    Transaction
};

/**
 * 
 */
class ResultTransformer extends TransformerAbstract
{
    public function transform (Result $result) {
        $mins = floor($result->best_lap_time / 60 % 60);
        $secs = floor($result->best_lap_time % 60);
        $milliSecs = (int)((($result->best_lap_time) - floor($result->best_lap_time)) * 1000);
        $best_lap_time = sprintf('%1d:%02d.%03d', $mins, $secs, $milliSecs);

        return [
            'session' => $result->session,
            'driver' => $result->driver,
            'team' => $result->team,
            'position' => $result->position,
            'event' => $result->session->event,
            'grid' => $result->grid,
            'result_status' => $result->result_status,
            'result_status_string' => $result->resultString,
            'positions_gained' => $result->grid - $result->position,
            'penalties' => $result->penalties,
            'fastest_lap' => $result->fastest_lap,
            'laps' => $result->laps,
            'stints' => $result->stints,
            'best_lap_time' => $best_lap_time,
            'points' => $result->points,
            'youtube' => $result->youtube,
            'eventPoints' => $result->eventPoints,
            'sessionLaps' => $result->sessionLaps,
            'isRace' => $result->session->isRace,
            'gapToWinner' => [
                "value" => $result->gap,
                "string" => $this->formatGapString($result->gap)
            ],
            'race_time' => [
                "value" => $result->race_time,
                "string" => $this->formatRaceTimeString($result->race_time)
            ],
            'fastest_lap_tyre' => $result->fastest_lap_tyre
        ];
    }

    public function formatGapString($value) {
        $mins = abs(intval(floor($value / 60 % 60)));
        $secs = abs(intval(floor($value % 60)));
        $milliSecs = abs((int)((($value) - floor($value)) * 1000));
        $string = sprintf('0.%03d', $milliSecs);

        if ($secs and $value) {
            $string = sprintf('%01d.%03d', $secs, $milliSecs);
        }
        if ($mins and $value) {
            $string = sprintf('%1d:%02d.%03d', $mins, $secs, $milliSecs);
        }

        return $string;
    }

    public function formatRaceTimeString($value) {
        $mins = abs(intval(floor($value / 60)));
        $secs = abs(intval(floor($value % 60)));
        $milliSecs = abs(intval((($value) - floor($value)) * 1000));

        
        return sprintf('%1d:%02d.%03d', $mins, $secs, $milliSecs);
    }

}