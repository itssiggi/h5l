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
    $this->get('/addEvent', AdminController::class . ':getAddEvent')->setName('admin.addEvent');
    $this->post('/addEvent', AdminController::class . ':postAddEvent')->setName('admin.addEvent');

    $this->get('/editEventResult/{event_id}', AdminController::class . ':getEditEventResult')->setName('admin.editEventResult');
    $this->post('/editEventResult/{event_id}', AdminController::class . ':postEditEventResult')->setName('admin.editEventResult');

    $this->get('/addEventwithResults', AdminController::class . ':getAddEventwithResults')->setName('admin.addEventwithResults');
    $this->post('/addEventwithResults', AdminController::class . ':postAddEventwithResults')->setName('admin.addEventwithResults');
});


/*$app->group('/auth', function () {
    $this->post('/login', AuthController::class . ':postSignIn')->setName('auth.signin');
    $this->get('/login', AuthController::class . ':getSignIn')->setName('auth.signin');

    $this->get('/signup', AuthController::class . ':getSignUp')->setName('auth.signup');
    $this->post('/signup', AuthController::class . ':postSignUp');

    $this->get('/me', AuthController::class . ':showMe')->setName('auth.me');
    $this->get('/me/edit', AuthController::class . ':showMeEdit')->setName('auth.me.edit');
    $this->post('/me/edit', AuthController::class . ':postMeEdit');
});*/

# $app->group('/teams', function () {
#     $this->get('', TeamController::class . ':index');
#     $this->get('/{name}', TeamController::class . ':show');
#     $this->delete('/{name}', TeamController::class . ':destroy');
# });



#$app->get('/setups/{id}', EventController::class . ':showSetup')->setName('events.showSetup');

# $app->group('/circuits', function () {
#     $this->get('', CircuitController::class . ':index');
#     $this->get('/{id}', CircuitController::class . ':show');
#     $this->delete('/{id}', CircuitController::class . ':destroy');
#     $this->post('/add', CircuitController::class . ':add');
# });

# $app->group('/seasons', function () {
#     $this->get('/{year}', SeasonController::class . ':show');
#     $this->get('/{year}/standings', SeasonController::class . ':standings');
#     $this->get('/{year}/standings/race/{race_id}', SeasonController::class . ':standings');
# });
