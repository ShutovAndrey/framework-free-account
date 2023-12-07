<?php

namespace Tests\Actions;

use App\Enums\GiftType;
use App\Models\Gift;
use App\Models\Settings;
use App\Models\User;
use Tests\TestCase;
use Tests\Traits\AppTestTrait;

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
        $user->password = \password_hash('test', PASSWORD_BCRYPT);
        $user->save();

        $userId = $user->id;

        $token = $this->getToken($userId);

        $request = $this->createRequest('GET', '/gift', ['Authorization' => "Bearer $token"]);

        $response = $this->app->handle($request);
        $actual = (string) $response->getBody();
        $gift = (array) \json_decode($actual, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertArrayHasKey('type', $gift);
        self::assertCount(3, $gift);
        self::assertContains($gift['type'], \array_column(GiftType::cases(), 'value'));
        if (GiftType::CACHE == $gift['type']) {
            $max = Settings::first('cache_max')->cache_max;
            self::assertLessThan($max, $gift['amount']);
        }
        if (GiftType::POINTS == $gift['type']) {
            $max = Settings::first('points_max')->points_max;
            self::assertLessThan($max, $gift['points']);
        }
    }

    public function testGiftFailAction(): void
    {
        $user = new User();
        $user->name = 'test1';
        $user->email = 'test1@test.test';
        $user->address = 'test1 str';
        $user->bank_account = '1234567';
        $user->password = \password_hash('test1', PASSWORD_BCRYPT);
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
        $user->password = \password_hash('test2', PASSWORD_BCRYPT);
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
        $actual = (string) $response->getBody();
        $giftFromResponse = (array) \json_decode($actual, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($gift->type, $giftFromResponse['type']);
        self::assertSame($gift->amount, $giftFromResponse['amount']);
    }
}
