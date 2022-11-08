<?php

namespace tests\Actions;

use Tests\Traits\AppTestTrait;
use Tests\TestCase;
use App\Enums\GiftType;
use App\Models\{
    Gift,
    User,
};

final class ConvertGiftActionTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @return void
     */
    public function testConvertGiftAction(): void
    {
        $user = new User();
        $user->name = 'test3';
        $user->email = 'test3@test.test';
        $user->address = 'test3 str';
        $user->bank_account = '1234567';
        $user->rate = 0.7;
        $user->password = password_hash('test3', PASSWORD_BCRYPT);
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

        $points = (int) round($gift->amount * $user->rate);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($points, $newGift->points);
        $this->assertNull($oldGift);
        $this->assertNotNull($newGift);
    }
}
