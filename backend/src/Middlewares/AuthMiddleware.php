<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use App\Services\JwtService;

class AuthMiddleware
{
    /**
     * @var JwtService
     */
    private $jwt;

    /**
     * The constructor.
     *
     * @param JwtService $jwt The JWT auth
     */
    public function __construct(JwtService $jwt)
    {
        $this->jwt = $jwt;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {

        if ($this->needTokenCheck($request)) {
            $token = explode(' ', (string) $request->getHeaderLine('Authorization'))[1] ?? '';

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
        return $request->getServerParams()['REQUEST_URI'] !== '/api/auth' &&
            $request->getServerParams()['REQUEST_METHOD'] !== 'OPTIONS';
    }
}
