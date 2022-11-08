<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

class CORSMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $this->setCORSHeaders($request);
        return $next($request);
    }

    protected function setCORSHeaders(ServerRequestInterface $request): void
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE,OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type,Authorization,X-Requested-With,Accept,Origin',
        ];

        if ($diff = array_diff_key($headers, $request->getHeaders())) {
            foreach ($diff as $name => $value) {
                header("{$name}: {$value}");
            }
        }
    }
}
