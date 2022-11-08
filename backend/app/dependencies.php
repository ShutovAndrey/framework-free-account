<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use App\Services\JwtService;
use App\Services\PaymentService;
use Psr\Container\ContainerInterface;
use Laminas\Diactoros\Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Actions\{
    GiftAction,
    AccountAction,
    TokenCreateAction,
    RefuseGiftAction,
    ConvertGiftAction,
    ConfirmGiftAction
};
use App\Middlewares\AuthMiddleware;
use App\Factory\LoggerFactory;


return function (ContainerBuilder $containerBuilder, $test = false) {
    $containerBuilder->addDefinitions([
        JwtService::class => function (ContainerInterface $container) {
            return new JwtService($container->get('settings')['jwt']);
        },
        PaymentService::class => function (ContainerInterface $container) {
            return new PaymentService(
                $container->get(Capsule::class),
                $container->get(LoggerFactory::class),
                $container->get('settings')['payments']
            );
        },
        JwtService::class => function (ContainerInterface $container) {
            return new JwtService($container->get('settings')['jwt']);
        },
        Capsule::class => function (ContainerInterface $container) use ($test) {
            $eloquent = new Capsule;
            $test ? $eloquent->addConnection($container->get('settings')['db_test']) :
                $eloquent->addConnection($container->get('settings')['db']);
            $eloquent->setAsGlobal();
            $eloquent->bootEloquent();
            $eloquent->setFetchMode(PDO::FETCH_ASSOC);
            return $eloquent;
        },
        LoggerFactory::class => function (ContainerInterface $container) {
            return new LoggerFactory($container->get('settings')['logger']);
        },

        GiftAction::class => function (ContainerInterface $container) {
            return new GiftAction(
                new Response(),
                $container->get(Capsule::class)
            );
        },
        RefuseGiftAction::class => function (ContainerInterface $container) {
            return new RefuseGiftAction(
                new Response(),
                $container->get(Capsule::class)
            );
        },

        ConvertGiftAction::class => function (ContainerInterface $container) {
            return new ConvertGiftAction(
                new Response(),
                $container->get(Capsule::class)
            );
        },
        ConfirmGiftAction::class => function (ContainerInterface $container) {
            return new ConfirmGiftAction(
                new Response(),
                $container->get(Capsule::class),
                $container->get(PaymentService::class)
            );
        },


        TokenCreateAction::class => function (ContainerInterface $container) {
            return new TokenCreateAction(
                new Response(),
                $container->get(Capsule::class),
                $container->get(JwtService::class)
            );
        },
        AccountAction::class => function (ContainerInterface $container) {
            return new AccountAction(
                new Response(),
                $container->get(Capsule::class),
            );
        },
        AuthMiddleware::class => function (ContainerInterface $container) {
            return new AuthMiddleware(
                $container->get(JwtService::class),
            );
        },
    ]);
};
