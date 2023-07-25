<?php

declare(strict_types=1);

use Laminas\Diactoros\ServerRequestFactory;
use Middlewares\RequestHandler;
use Relay\Relay;
use Middlewares\FastRoute;
use App\Middlewares\AuthMiddleware;
use App\Services\JwtService;
use App\Emitter\Emitter;

require __DIR__ . '/../vendor/autoload.php';
$container = require __DIR__ . '/../app/container.php';
$routes = require __DIR__ . '/../app/routes.php';

// Set up exception handler
set_exception_handler(function (\Throwable $exception) {
    header("HTTP/1.1 {$exception->getCode()} {$exception->getMessage()}");
});

$request = ServerRequestFactory::fromGlobals();

$middlewareQueue[] = new AuthMiddleware($container->get(JwtService::class));
$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);
$requestHandler = new Relay($middlewareQueue);

$response = $requestHandler->handle($request);

$emitter = new Emitter();
return $emitter->emit($response);
