<?php

declare(strict_types=1);

namespace Tests;

use App\Middlewares\AuthMiddleware;
use App\Services\JwtService;
use Laminas\Diactoros\ServerRequest as Request;
use Laminas\Diactoros\StreamFactory;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Relay\Relay;

abstract class TestCase extends PHPUnitTestCase
{
    protected Relay $app;

    protected \DI\Container $container;

    public function setUp(): void
    {
        require __DIR__ . '/../vendor/autoload.php';

        $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(\dirname(__DIR__));
        $dotenv->load();

        $containerBuilder = new \DI\ContainerBuilder();
        $containerBuilder->useAutowiring(false);
        $containerBuilder->useAnnotations(false);

        // Set up settings
        $settings = require __DIR__ . '/../app/settings.php';
        $settings($containerBuilder);

        // Set up dependencies
        $dependencies = require __DIR__ . '/../app/dependencies.php';
        $dependencies($containerBuilder, true);

        $this->container = $containerBuilder->build();
        $routes = require __DIR__ . '/../app/routes.php';

        $middlewareQueue[] = new AuthMiddleware($this->container->get(JwtService::class));
        $middlewareQueue[] = new FastRoute($routes);
        $middlewareQueue[] = new RequestHandler($this->container);
        $this->app = new Relay($middlewareQueue);
    }

    protected function createRequest(
        string $method,
        string $path,
        array $headers = [],
        array $body = []
    ): Request {
        $path = '/api' . $path;
        $serverParams = ['REQUEST_URI' => $path, 'REQUEST_METHOD' => $method];
        $handle = \fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $headers += [
            'HTTP_ACCEPT' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE,OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type,Authorization,X-Requested-With,Accept,Origin',
        ];

        return new Request(
            $serverParams,
            [],
            $path,
            $method,
            $stream,
            $headers,
            [],
            [],
            $body
        );
    }

    protected function getToken(int $id): string
    {
        $payload = [
            'uid' => $id,
            'role' => 'user',
            'action' => 'auth:access',
        ];
        $jwt = $this->container->get(JwtService::class);

        return $jwt->createJwt($payload);
    }
}
