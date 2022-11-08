<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'settings' => [
            'logger' => [
                'name' => 'app',
                'path' => __DIR__ . '/../logs/app.log',
                'level' => Logger::INFO,
                'file_permission' => 0775,
            ],
            'db' => [
                'driver'    => 'mysql',
                'host'      => getenv('DB_HOST'),
                'port'      => getenv('DB_PORT'),
                'database'  => getenv('DB_NAME'),
                'username'  => getenv('DB_USER'),
                'password'  => getenv('DB_PASS'),
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => ''
            ],
            'db_test' => [
                'driver'    => 'mysql',
                'host'      => getenv('DB_HOST'),
                'port'      => getenv('DB_PORT'),
                'database'  => getenv('DB_TEST_NAME'),
                'username'  => getenv('DB_USER'),
                'password'  => getenv('DB_PASS'),
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => ''
            ],
            'jwt' => [
                'lifetime' => getenv('JWT_LIFETIME'),
                'key' => getenv('JWT_KEY')
            ],

            'payments' => [
                'bank' => [
                    'apiKey' => getenv('BANK_API_KEY'),
                ],
            ],
        ],
    ]);
};
