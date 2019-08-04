<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;
use NotesGalleryApp\Middleware\AuthMiddleware;
use Relay\Relay;
use Zend\Diactoros\ServerRequestFactory;

// load environment variables from .env
$dotenv = Dotenv::create(dirname(__DIR__));
$dotenv->load();

// init DI container
$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

// FastRoute
$dispatcher = require_once dirname(__DIR__) . '/src/routes/PageRoutes.php';

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// handle query params
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$route = $dispatcher->dispatch(
    $httpMethod,
    $uri
);

switch ($route[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        die('404 not found!');

    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        die('405 method not allowed');

    // handle FOUND on relay
    case \FastRoute\Dispatcher::FOUND:
        break;
};

// Middlewares (Custom Auth, Route & Request handler)
$middlewareQueue = [];
$middlewareQueue[] = new AuthMiddleware();
$middlewareQueue[] = new FastRoute($dispatcher);
$middlewareQueue[] = new RequestHandler($container);

$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
return $emitter->emit($response);
