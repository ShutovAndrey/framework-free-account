<?php

namespace tests\Services;

use Tests\Traits\AppTestTrait;
use Tests\TestCase;
use App\Services\JwtService;

final class JwtServiceTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @return void
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

        $this->assertSame($token, $this->getToken(8));
        $this->assertTrue($jwt->validateToken($this->getToken(8)));
    }
}
