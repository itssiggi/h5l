<?php

use App\Controllers\ {
    SeasonController,
    DriverController,
    EventController,
    CircuitController,
    TeamController,
    SessionController,
    AdminController,
    Auth\AuthController
};

use App\Middleware\AuthMiddleware;

$app->group('/api', function () use ($app) {
    $app->group('/drivers', function () use ($app) {
        $app->post('', DriverController::class . ':apiCreateDriver')->setName('driver.create');
        $app->get('', DriverController::class . ':apiGetDrivers')->setName('drivers.get');
        $app->get('/{id}', DriverController::class . ':apiGetDriver')->setName('driver.get');
        $app->put('/{id}', DriverController::class . ':apiUpdateDriver')->setName('driver.update');
        $app->delete('', DriverController::class . ':apiDeleteDriver')->setName('driver.delete');
    });

    $app->group('/standings', function () use ($app) {
        $app->get('/standings', SeasonController::class . ':apiCurrentStandings');
    });

    $app->group('/sessions', function () use ($app) {
        $app->post('', SessionController::class . ':apiCreateSession')->setName('session.create');
        $app->get('', SessionController::class . ':apiGetSessions')->setName('sessions.get');
        $app->get('/{id}', SessionController::class . ':apiGetSession')->setName('session.get');
        $app->delete('', SessionController::class . ':apiDeleteSession')->setName('session.delete');
    });

    $app->group('/events', function () use ($app) {
        $app->post('', EventController::class . ':apiCreateEvent')->setName('event.create');
        $app->get('', EventController::class . ':apiGetEvents')->setName('events.get');
        $app->get('/{id}', EventController::class . ':apiGetEvent')->setName('event.get');
        $app->delete('', EventController::class . ':apiDeleteEvent')->setName('event.delete');
    });

    $app->group('/seasons', function () use ($app) {
        //$app->post('', SeasonController::class . ':apiCreateSeason')->setName('seasons.create');
        $app->get('', SeasonController::class . ':apiGetSeasons')->setName('seasons.get');
        $app->get('/{id}', SeasonController::class . ':apiGetSeason')->setName('season.get');
        // $app->delete('', SeasonController::class . ':apiDeleteSeason')->setName('seasons.delete');
    });

    $app->group('/penalties', function () use ($app) {
        $app->post('', SesssionElementController::class . ':apiCreatePenalties')->setName('penalties.create');
        $app->get('', SesssionElementController::class . ':apiGetPenalties')->setName('penalties.get');
        $app->get('/{id}', SesssionElementController::class . ':apiGetPenalty')->setName('penalty.get');
        $app->delete('', SesssionElementController::class . ':apiDeletePenalties')->setName('penalties.delete');
    });

    $app->group('/laptimes', function () use ($app) {
        $app->post('', SesssionElementController::class . ':apiCreateLaptimes')->setName('laptimes.create');
        $app->get('', SesssionElementController::class . ':apiGetLaptime')->setName('laptimes.get');
        $app->get('/{id}', SesssionElementController::class . ':apiGetLaptime')->setName('laptime.get');
        $app->delete('', SesssionElementController::class . ':apiDeleteLaptimes')->setName('laptimes.delete');
    });

    $app->group('/phases', function () use ($app) {
        $app->post('', SesssionElementController::class . ':apiCreatePhase')->setName('phases.create');
        $app->get('', SesssionElementController::class . ':apiGetPhases')->setName('phases.get');
        $app->get('/{id}', SesssionElementController::class . ':apiGetPhase')->setName('phases.get');
        $app->delete('', SesssionElementController::class . ':apiDeletePhase')->setName('phases.delete');
    });
});



?>