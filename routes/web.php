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

$app->get('', EventController::class . ':index')->setName('index');


$app->get('/standings', SeasonController::class . ':currentStandings')->setName('standings.index');
$app->get('/rules', SeasonController::class . ':getRules')->setName('rules');

$app->group('/events', function () {
    $this->get('', EventController::class . ':index')->setName('events.index');
    $this->get('/{id}', EventController::class . ':show')->setName('events.show');
});

$app->group('/drivers', function () {
    #$this->get('', EventController::class . ':index')->setName('drivers.index');
    #$this->get('/{name}', EventController::class . ':index')->setName('drivers.show');
    $this->get('', DriverController::class . ':index')->setName('drivers.index');
    $this->get('/{name}', DriverController::class . ':show')->setName('drivers.show');
});

$app->group('/sessions', function () {
    $this->get('/{id}', SessionController::class . ':show')->setName('sessions.show');
});

$app->group('/admin', function () {
    $this->get('', AdminController::class . ':getIndex')->setName('admin.index');

    $this->get('/addEvent', AdminController::class . ':getAddEvent')->setName('admin.addEvent');
    $this->post('/addEvent', AdminController::class . ':postAddEvent')->setName('admin.addEvent');

    $this->get('/editEventResult/{event_id}', AdminController::class . ':getEditEventResult')->setName('admin.editEventResult');
    $this->post('/editEventResult/{event_id}', AdminController::class . ':postEditEventResult')->setName('admin.editEventResult');

    $this->get('/addEventWithResults', AdminController::class . ':getAddEventwithResults')->setName('admin.addEventwithResults');
    $this->post('/addEventWithResults', AdminController::class . ':postAddEventwithResults')->setName('admin.addEventwithResults');

    # Penalties
    $this->get('/sessions/{session_id}/editPenalties', AdminController::class . ':getEditPenalties')->setName('admin.editPenalties');
    $this->post('/sessions/{session_id}/editPenalties', AdminController::class . ':postEditPenalties')->setName('admin.editPenalties');
    $this->post('/sessions/{session_id}/addPenalty', AdminController::class . ':postAddPenalty')->setName('admin.addPenalty');

    $this->get('/invalidatePenalty/{id}', AdminController::class . ':invalidatePenalty')->setName('admin.invalidatePenalty');
    $this->get('/validatePenalty/{id}', AdminController::class . ':validatePenalty')->setName('admin.validatePenalty');

    $this->get('/events/deleteResults', EventController::class . ':deleteEventResults')->setName('admin.deleteEventResults.index');
    $this->get('/events/deleteResults/{id}', EventController::class . ':deleteEventResults')->setName('admin.deleteEventResults');

    // Standings
    $this->get('/standings/recalculate', AdminController::class . ':getRecalculateStandings')->setName('admin.recalculateStandings');
})->add(new AuthMiddleware($container));;

$app->group('/auth', function () {
    $this->get('/logout', AuthController::class . ':getSignOut')->setName('auth.signout');
    $this->get('/me', AuthController::class . ':showMe')->setName('auth.me');
    $this->get('/me/edit', AuthController::class . ':showMeEdit')->setName('auth.me.edit');
    $this->post('/me/edit', AuthController::class . ':postMeEdit');
})->add(new AuthMiddleware($container));

$app->group('/auth', function () {
    $this->post('/login', AuthController::class . ':postSignIn')->setName('auth.signin');
    $this->get('/login', AuthController::class . ':getSignIn')->setName('auth.signin');
    $this->get('/signup', AuthController::class . ':getSignUp')->setName('auth.signup');
    $this->post('/signup', AuthController::class . ':postSignUp');
});

?>