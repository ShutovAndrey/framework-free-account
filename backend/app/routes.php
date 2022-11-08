<?php

declare(strict_types=1);

use function FastRoute\simpleDispatcher;
use FastRoute\RouteCollector;
use Laminas\Diactoros\Response;


return simpleDispatcher(function (RouteCollector $r) {
    $r->addGroup('/api', function (RouteCollector $r) {
        //CORS
        $r->addRoute('OPTIONS', '/{routes:.*}', function () {
            return new Response;
        });

        $r->addGroup('/gift', function (RouteCollector $r) {
            $r->get('', \App\Actions\GiftAction::class);
            $r->delete('/{id:\d+}', \App\Actions\RefuseGiftAction::class);
            $r->put('/{id:\d+}', \App\Actions\ConvertGiftAction::class);
            $r->put('/{id:\d+}/confirm', \App\Actions\ConfirmGiftAction::class);
        });
        $r->get('/account', \App\Actions\AccountAction::class);
        $r->post('/auth', \App\Actions\TokenCreateAction::class);
    });
});
