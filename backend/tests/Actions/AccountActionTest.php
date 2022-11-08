<?php

namespace tests\Actions;

use Tests\Traits\AppTestTrait;
use Tests\Traits\MockTrait;
use Tests\TestCase;

final class AccountActionTest extends TestCase
{
    use AppTestTrait;
    use MockTrait;

    protected $token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOjEsInJvbGUiOiJ1c2VyIiwiYWN0aW9uIjoiYXV0aDphY2Nlc3MiLCJleHAiOjE3NjczMDAyMTR9.NsoC82RHGc9yDzFAsZZxr2rwdPJIEGS9qe6rSrBi-H8';

    /**
     * Test.
     *
     * @dataProvider provideAccountAction
     *
     * @param array $expected The expected result
     *
     * @return void
     */
    public function testAccountAction(array $expected): void
    {
        $request = $this->createRequest('GET', '/account', ['Authorization' => $this->token]);

        $response = $this->app->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonData($expected, $response);
    }

    /**
     * Provider.
     *
     * @return array The data
     */
    public function provideAccountAction(): array
    {
        return [
            'Response' => [
                [
                    'id' => 1,
                    'email' => 'user1@user.com',
                    'name' => 'user1',
                    'points' => 0,
                ],
            ],
        ];
    }
}
