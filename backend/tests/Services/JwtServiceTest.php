<?php

namespace Tests\Services;

use App\Services\JwtService;
use DI\DependencyException;
use DI\NotFoundException;
use Tests\TestCase;
use Tests\Traits\AppTestTrait;

final class JwtServiceTest extends TestCase
{
    use AppTestTrait;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testCreateJwt(): void
    {
        $payload = [
            'uid' => 8,
            'role' => 'user',
            'action' => 'auth:access',
        ];
        $jwt = $this->container->get(JwtService::class);
        $token = $jwt->createJwt($payload);

        self::assertSame($token, $this->getToken(8));
        self::assertTrue($jwt->validateToken($this->getToken(8)));
    }
}
