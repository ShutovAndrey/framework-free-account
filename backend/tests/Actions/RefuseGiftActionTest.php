<?php

namespace Tests\Actions;

use App\Models\Gift;
use App\Models\Good;
use App\Models\User;
use Tests\TestCase;
use Tests\Traits\AppTestTrait;

final class RefuseGiftActionTest extends TestCase
{
    use AppTestTrait;

    public function testConvertGiftAction(): void
    {
        $userId = User::inRandomOrder()->first('id')->id;
        $goodId = Good::inRandomOrder()->first('id')->id;
        $gift = new Gift();
        $gift->user_id = $userId;
        $gift->good_id = $goodId;
        $gift->type = 3;
        $gift->save();

        $createdGift = Gift::whereId($gift->id)->first();

        $token = $this->getToken($userId);

        $request = $this->createRequest('DELETE', "/gift/{$gift->id}", ['Authorization' => "Bearer $token"]);

        $response = $this->app->handle($request);

        $oldGift = Gift::whereId($gift->id)->first();

        self::assertSame(200, $response->getStatusCode());
        self::assertNotNull($createdGift);
        self::assertNull($oldGift);
    }
}
