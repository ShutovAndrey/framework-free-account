<?php

declare(strict_types=1);

namespace App\Enums;

enum GiftType: int
{
    case CACHE = 1;
    case POINTS = 2;
    case GOOD = 3;
}
