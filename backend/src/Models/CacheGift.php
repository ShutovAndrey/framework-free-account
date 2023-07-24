<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GiftType;
use App\Interfaces\GiftInterface;

class CacheGift extends Gift implements GiftInterface
{
    public function getAttributes(): array
    {
        return [
            'type' => GiftType::CACHE->value,
            'amount' => rand(0, (Settings::first('cache_max')->cache_max)),
        ];
    }

    public static function isAvailable(): bool
    {
        return Settings::whereColumn('cache_fund', '>=', 'cache_max')->exists();
    }
}
