<?php

declare(strict_types=1);

namespace App\Enums;

enum DeliveryStatus: int
{
    case SENDING = 1;
    case SENT = 2;
    case DELIVERED = 3;
}


