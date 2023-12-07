<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Services\JwtService;
use Psr\Http\Message\ServerRequestInterface;

class AuthMiddleware
{
    private JwtService $jwt;

    public function __construct(JwtService $jwt)
    {
        $this->jwt = $jwt;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {

        if ($this->needTokenCheck($request)) {
            $token = \explode(' ', (string) $request->getHeaderLine('Authorization'))[1] ?? '';

            if ($token && $this->jwt->validateToken($token)) {
                $request = $request->withAttribute('uid', (int) $this->jwt->getClaim('uid'));
            } else {
                throw new \App\Exception\UnauthenticatedException();
            }
        }

        return $next($request);
    }

    protected function needTokenCheck(ServerRequestInterface $request): bool
    {
        return '/api/auth' !== $request->getServerParams()['REQUEST_URI']
            && 'OPTIONS' !== $request->getServerParams()['REQUEST_METHOD'];
    }
}
