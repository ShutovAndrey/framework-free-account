<?php

namespace Tests\Actions;

use App\Enums\DeliveryStatus;
use App\Models\Delivery;
use App\Models\Gift;
use App\Models\Good;
use App\Models\GoodsStore;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;
use Tests\Traits\AppTestTrait;

final class ConfirmGiftActionTest extends TestCase
{
    use AppTestTrait;

    public function testConfirmGiftGoodAction(): void
    {
        $userId = User::inRandomOrder()->first('id')->id;
        $goodId = Good::inRandomOrder()->first('id')->id;
        $gift = new Gift();
        $gift->user_id = $userId;
        $gift->good_id = $goodId;
        $gift->type = 3;
        $gift->save();

        $quantityOld = GoodsStore::where('good_id', $goodId)->first('quantity')->quantity;

        $token = $this->getToken($userId);

        $request = $this->createRequest(
            'PUT',
            "/gift/{$gift->id}/confirm",
            ['Authorization' => "Bearer $token"]
        );

        $response = $this->app->handle($request);

        $updatedGift = Gift::whereId($gift->id)->first();

        $quantityNew = GoodsStore::where('good_id', $goodId)->first('quantity')->quantity;

        $deliveryExists = Delivery::where([
            ['good_id', $goodId],
            ['user_id', $userId],
            ['status', DeliveryStatus::SENDING],
        ])
            ->exists()
        ;

        self::assertSame(200, $response->getStatusCode());
        self::assertNull(\json_decode($response->getBody()));
        self::assertSame(1, $updatedGift->confirmed);
        self::assertSame($quantityOld - 1, $quantityNew);
        self::assertTrue($deliveryExists);
    }

    public function testConfirmGiftCacheAction(): void
    {
        $userId = User::inRandomOrder()->first('id')->id;
        $gift = new Gift();
        $gift->user_id = $userId;
        $gift->amount = 777;
        $gift->type = 1;
        $gift->save();

        $balanceOld = Settings::first('cache_fund')->cache_fund;

        $token = $this->getToken($userId);

        $request = $this->createRequest(
            'PUT',
            "/gift/{$gift->id}/confirm",
            ['Authorization' => "Bearer $token"]
        );

        $response = $this->app->handle($request);

        $balanceNew = Settings::first('cache_fund')->cache_fund;

        $updatedGift = Gift::whereId($gift->id)->first();

        $transactionExists = Transaction::where([
            ['amount', $gift->amount],
            ['user_id', $userId],
        ])
            ->exists()
        ;

        self::assertSame(200, $response->getStatusCode());
        self::assertNull(\json_decode($response->getBody()));
        self::assertSame(1, $updatedGift->confirmed);
        self::assertSame($balanceOld - $gift->amount, $balanceNew);
        self::assertTrue($transactionExists);
    }

    public function testConfirmGiftPointsAction(): void
    {
        $user = User::inRandomOrder()->first('id');
        $userId = $user->id;
        $gift = new Gift();
        $gift->user_id = $userId;
        $gift->points = 333;
        $gift->type = 2;
        $gift->save();

        $balanceOld = $user->points;

        $token = $this->getToken($userId);

        $request = $this->createRequest(
            'PUT',
            "/gift/{$gift->id}/confirm",
            ['Authorization' => "Bearer $token"]
        );

        $response = $this->app->handle($request);

        $balanceNew = User::whereId($userId)->first('points')->points;

        $updatedGift = Gift::whereId($gift->id)->first();

        self::assertSame(200, $response->getStatusCode());
        self::assertNull(\json_decode($response->getBody()));
        self::assertSame(1, $updatedGift->confirmed);
        self::assertSame($balanceOld + $gift->points, $balanceNew);
    }
}
