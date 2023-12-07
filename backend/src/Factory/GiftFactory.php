<?php

declare(strict_types=1);

namespace App\Factory;

use App\Interfaces\GiftInterface;
use App\Models\CacheGift;
use App\Models\GoodsGift;
use App\Models\PointsGift;

class GiftFactory
{
    public function getRandomGift(): GiftInterface
    {
        $giftTypes = [GoodsGift::class, CacheGift::class, PointsGift::class];

        return $this->chooseGift($giftTypes);
    }

    private function chooseGift(array $giftTypes): GiftInterface
    {
        $index = \mt_rand(0, \count($giftTypes) - 1);
        /** @var GiftInterface $giftType */
        $giftType = $giftTypes[$index];
        if ($giftType::isAvailable()) {
            return new $giftType();
        }
        unset($giftTypes[$index]);

        return $this->chooseGift($giftTypes);

    }
}
