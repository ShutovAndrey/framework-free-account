<?php

namespace Tests\Actions;

use App\Enums\GiftType;
use App\Models\Gift;
use App\Models\User;
use Tests\TestCase;
use Tests\Traits\AppTestTrait;

final class ConvertGiftActionTest extends TestCase
{
    use AppTestTrait;

    public function testConvertGiftAction(): void
    {
        $user = new User();
        $user->name = 'test3';
        $user->email = 'test3@test.test';
        $user->address = 'test3 str';
        $user->bank_account = '1234567';
        $user->rate = 0.7;
        $user->password = \password_hash('test3', PASSWORD_BCRYPT);
        $user->save();

        $userId = $user->id;

        $gift = new Gift();
        $gift->user_id = $userId;
        $gift->amount = 500;
        $gift->type = 1;
        $gift->save();

        $token = $this->getToken($userId);

        $request = $this->createRequest('PUT', "/gift/{$gift->id}", ['Authorization' => "Bearer $token"]);

        $response = $this->app->handle($request);

        $oldGift = Gift::whereId($gift->id)->first();
        $newGift = Gift::where([['user_id', $userId], ['type', GiftType::POINTS->value], ['confirmed', true]])->first();

        $points = (int) \round($gift->amount * $user->rate);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($points, $newGift->points);
        self::assertNull($oldGift);
        self::assertNotNull($newGift);
    }
}
