<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;
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

$route = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
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

// Middlewares (Route & Request handler)
$middlewareQueue = [];
$middlewareQueue[] = new FastRoute($dispatcher);
$middlewareQueue[] = new RequestHandler($container);

$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
return $emitter->emit($response);
