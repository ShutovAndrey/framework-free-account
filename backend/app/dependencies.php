<?php

declare(strict_types=1);

use App\Factory\GiftProcessorFactory;
use App\Services\GiftProcessor\CacheGiftProcessor;
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


return static fn (ContainerBuilder $containerBuilder, $test = false) =>
    $containerBuilder->addDefinitions([
        JwtService::class => static fn (ContainerInterface $container) =>
             new JwtService($container->get('settings')['jwt']),
        PaymentService::class => static fn (ContainerInterface $container) =>
             new PaymentService(
                $container->get(Capsule::class),
                $container->get(LoggerFactory::class),
                $container->get('settings')['payments']
            ),
        Capsule::class => function (ContainerInterface $container) use ($test) {
            $eloquent = new Capsule;
            $test ? $eloquent->addConnection($container->get('settings')['db_test']) :
                $eloquent->addConnection($container->get('settings')['db']);
            $eloquent->setAsGlobal();
            $eloquent->bootEloquent();
            $eloquent->setFetchMode(PDO::FETCH_ASSOC);
            return $eloquent;
        },
        LoggerFactory::class => static fn (ContainerInterface $container) =>
             new LoggerFactory($container->get('settings')['logger']),

        GiftProcessorFactory::class => static fn (ContainerInterface $container)=>
            new GiftProcessorFactory($container->get(PaymentService::class)),

        GiftAction::class => static fn (ContainerInterface $container) =>
             new GiftAction(
                new Response(),
                $container->get(Capsule::class)
            ),
        RefuseGiftAction::class => static fn (ContainerInterface $container) =>
             new RefuseGiftAction(
                new Response(),
                $container->get(Capsule::class)
            ),

        ConvertGiftAction::class => static fn (ContainerInterface $container) =>
             new ConvertGiftAction(
                new Response(),
                $container->get(Capsule::class)
            ),

        ConfirmGiftAction::class => static fn (ContainerInterface $container) =>
             new ConfirmGiftAction(
                new Response(),
                $container->get(Capsule::class),
                $container->get(GiftProcessorFactory::class)
            ),

        TokenCreateAction::class => static fn (ContainerInterface $container) =>
             new TokenCreateAction(
                new Response(),
                $container->get(Capsule::class),
                $container->get(JwtService::class)
            ),

        AccountAction::class => static fn (ContainerInterface $container) =>
             new AccountAction(
                new Response(),
                $container->get(Capsule::class),
            ),

        AuthMiddleware::class => static fn (ContainerInterface $container) =>
             new AuthMiddleware(
                $container->get(JwtService::class),
            ),
    ]);
