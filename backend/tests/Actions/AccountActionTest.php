<?php

namespace Tests\Actions;

use Tests\Traits\AppTestTrait;
use Tests\Traits\MockTrait;
use Tests\TestCase;

final class AccountActionTest extends TestCase
{
    use AppTestTrait;
    use MockTrait;

    protected string $token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOjEsInJvbGUiOiJ1c2VyIiwiYWN0aW9uIjoiYXV0aDphY2Nlc3MiLCJleHAiOjE3NjgxNTExODB9.kT-GS9Jc-mCWXn_RW78Eda2oIUWN-K5KBzTgGkq3oZw';

    /**
     * Test.
     *
     * @dataProvider provideAccountAction
     *
     * @param array $expected The expected result
     *
     * @return void
     * @throws \JsonException
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
