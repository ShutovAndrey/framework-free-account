<?php

namespace Tests\Actions;

use Tests\Traits\AppTestTrait;
use Tests\TestCase;
use App\Enums\GiftType;
use App\Models\{
    Gift,
    User,
    Settings
};

final class GiftActionTest extends TestCase
{
    use AppTestTrait;

    public function testGiftAction(): void
    {
        $user = new User();
        $user->name = 'test';
        $user->email = 'test@test.test';
        $user->address = 'test str';
        $user->bank_account = '123456';
        $user->password = password_hash('test', PASSWORD_BCRYPT);
        $user->save();

        $userId = $user->id;

        $token = $this->getToken($userId);

        $request = $this->createRequest('GET', '/gift', ['Authorization' => "Bearer $token"]);

        $response = $this->app->handle($request);
        $actual = (string)$response->getBody();
        $gift = (array)json_decode($actual, true, 512, JSON_THROW_ON_ERROR);


        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('type', $gift);
        $this->assertCount(3, $gift);
        $this->assertContains($gift['type'], array_column(GiftType::cases(), 'value'));
        if ($gift['type'] == GiftType::CACHE) {
            $max = Settings::first('cache_max')->cache_max;
            $this->assertLessThan($max, $gift['amount']);
        }
        if ($gift['type'] == GiftType::POINTS) {
            $max = Settings::first('points_max')->points_max;
            $this->assertLessThan($max, $gift['points']);
        }
    }

    public function testGiftFailAction(): void
    {
        $user = new User();
        $user->name = 'test1';
        $user->email = 'test1@test.test';
        $user->address = 'test1 str';
        $user->bank_account = '1234567';
        $user->password = password_hash('test1', PASSWORD_BCRYPT);
        $user->save();

        $userId = $user->id;

        $gift = new Gift();
        $gift->user_id = $userId;
        $gift->amount = 500;
        $gift->type = 1;
        $gift->confirmed = true;
        $gift->save();

        $token = $this->getToken($userId);

        $request = $this->createRequest('GET', '/gift', ['Authorization' => "Bearer $token"]);

        $this->expectException(\App\Exception\ValidationException::class);
        $response = $this->app->handle($request);
    }

    public function testGiftNotConfirmedAction(): void
    {
        $user = new User();
        $user->name = 'test2';
        $user->email = 'test2@test.test';
        $user->address = 'test2 str';
        $user->bank_account = '1234567';
        $user->password = password_hash('test2', PASSWORD_BCRYPT);
        $user->save();

        $userId = $user->id;

        $gift = new Gift();
        $gift->user_id = $userId;
        $gift->amount = 333;
        $gift->type = 1;
        $gift->save();

        $token = $this->getToken($userId);

        $request = $this->createRequest('GET', '/gift', ['Authorization' => "Bearer $token"]);
        $response = $this->app->handle($request);
        $actual = (string)$response->getBody();
        $giftFromResponse = (array)json_decode($actual, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($gift->type, $giftFromResponse['type']);
        $this->assertSame($gift->amount, $giftFromResponse['amount']);
    }
}
