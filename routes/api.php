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

$app->group('/api', function () {
    $this->get('/standings', SeasonController::class . ':apiCurrentStandings');
    $this->get('/drivers', DriverController::class . ':apiIndex');
});

?>