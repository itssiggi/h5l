<?php

use \Interop\Container\ContainerInterface as ContainerInterface;
use \App\Middleware\Cors;
use \App\Middleware\ValidationErrorsMiddleware;
use \App\Middleware\SessionMiddleware;
use \App\Middleware\OldInputMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

require_once __DIR__ . '/database.php';

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => false,

        'app' => [
            'name' => getenv('APP_NAME')
        ],

        'views' => [
            'cache' => getenv('VIEW_CACHE_DISABLED') === 'true' ? false : __DIR__ . '/../storage/views'
        ],
        'public' => '/'
    ],
]);


$container = $app->getContainer();
$container["session"] = session_start();

# $app->add(new Cors($container));
# $app->add(new SessionMiddleware($container));


$container["fractal"] = function () {
    return new \League\Fractal\Manager();
};

if (isset($_GET['include'])) {
    $container["fractal"]->parseIncludes($_GET['include']);
}

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        # 'cache' => $container->settings['views']['cache']
        'debug' => true,
    ]);

    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->addExtension(new \Twig\Extension\DebugExtension());

    return $view;
};

$container['validator'] = function ($container) {
    return new \App\Validation\Validator();
};

$container['auth'] = function ($container) {
    return new \App\Auth\Auth();
};

$app->add(new ValidationErrorsMiddleware($container));
$app->add(new OldInputMiddleware($container));

require_once __DIR__ . '/../routes/web.php';

