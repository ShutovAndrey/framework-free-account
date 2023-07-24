<?php

declare(strict_types=1);

namespace App\Factory;

use App\Interfaces\GiftInterface;
use App\Models\{
    GoodsGift,
    CacheGift,
    PointsGift
};

class GiftFactory
{
    public function getRandomGift(): GiftInterface
    {
        $giftTypes = [GoodsGift::class, CacheGift::class, PointsGift::class];
        foreach ($giftTypes as $index => $type) {
            if (!$type::isAvailable()) {
                unset($giftTypes[$index]);
            }
        }
        return new $giftTypes[rand(0, count($giftTypes) - 1)]();
    }
}
