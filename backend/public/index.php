<?php

declare(strict_types=1);

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Middlewares\RequestHandler;
use Relay\Relay;
use Middlewares\FastRoute;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\CORSMiddleware;
use App\Services\JwtService;


require __DIR__ . '/../vendor/autoload.php';
$container = require __DIR__ . '/../app/container.php';
$routes = require __DIR__ . '/../app/routes.php';

// Set up exeption handler
set_exception_handler(function (\Throwable $exception) {
    header("HTTP/1.1 {$exception->getCode()} {$exception->getMessage()}");
});

$request = ServerRequestFactory::fromGlobals();

$middlewareQueue[] = new CORSMiddleware();
$middlewareQueue[] = new AuthMiddleware($container->get(JwtService::class));
$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);
$requestHandler = new Relay($middlewareQueue);

$response = $requestHandler->handle($request);

$emitter = new SapiEmitter();
return $emitter->emit($response);
