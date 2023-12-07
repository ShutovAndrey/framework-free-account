<?php

declare(strict_types=1);

namespace App\Interfaces;

interface GiftInterface
{
    public static function isAvailable(): bool;

    public function getAttributes(): array;
}
