<?php

declare(strict_types=1);

namespace App\Interfaces;

interface GiftInterface
{
    public function getAttributes(): array;
    public static function isAvailable(): bool;
}
