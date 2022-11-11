<?php

namespace tests\Actions;

use Tests\Traits\AppTestTrait;
use Tests\TestCase;


final class TokenCreateActionTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @dataProvider provideTokenCreateAction
     *
     * @return void
     */
    public function testTokenCreateAction(array $expected): void
    {
        $body = [
            'email' => 'user1@user.com',
            'password' => 'qwerty1',
        ];

        $request = $this->createRequest(
            'POST',
            '/auth',
            [],
            $body
        );

        $response = $this->app->handle($request);
        $expected['access_token'] = $this->getToken(1);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertJsonData($expected, $response);
    }

    /**
     * @expectedException UnauthenticatedException
     */
    public function testTokenCreateFailAction(): void
    {
        $body = [
            'email' => 'user1@user.com',
            'password' => 'wrongPass',
        ];

        $request = $this->createRequest(
            'POST',
            '/auth',
            [],
            $body
        );

        $this->expectException(\App\Exception\UnauthenticatedException::class);
        $response = $this->app->handle($request);
    }

    /**
     * Provider.
     *
     * @return array The data
     */
    public function provideTokenCreateAction(): array
    {

        return [
            'Response' => [
                [
                    'access_token' => '',
                    'token_type' => 'Bearer',
                ],
            ],
        ];
    }
}
