<?php

declare(strict_types=1);

namespace App\Services\GiftProcessor;

use App\Enums\DeliveryStatus;
use App\Interfaces\GiftProcessorInterface;
use App\Models\Delivery;
use App\Models\Gift;
use App\Models\GoodsStore;
use App\Models\User;

final class GoodsGiftProcessor implements GiftProcessorInterface
{
    public function process(User $user, Gift $gift): void
    {
        $store = GoodsStore::firstWhere('good_id', $gift->good_id);
        $store->decrement('quantity');

        $delivery = new Delivery();
        $delivery->good_id = $gift->good_id;
        $delivery->user_id = $user->id;
        $delivery->address = $user->address;
        $delivery->status = DeliveryStatus::SENDING;
        $delivery->save();
    }
}
