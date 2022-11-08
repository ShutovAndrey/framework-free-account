<?php

declare(strict_types=1);

$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__));
$dotenv->load();

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

return $containerBuilder->build();
