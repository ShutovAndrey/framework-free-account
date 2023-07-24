<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GiftType;
use App\Interfaces\GiftInterface;

class PointsGift extends Gift implements GiftInterface
{
    public function getAttributes(): array
    {
        return [
            'type' => GiftType::POINTS->value,
            'points' => rand(0, Settings::first('points_max')->points_max),
        ];
    }

    public static function isAvailable(): bool
    {
        return true;
    }
}
