<?php

declare(strict_types=1);

namespace App\Services\GiftProcessor;

use App\Interfaces\GiftProcessorInterface;
use App\Models\Gift;
use App\Models\User;

final class PointsGiftProcessor implements GiftProcessorInterface
{
    public function process(User $user, Gift $gift): void
    {
        $user->update(['points' => $user->points + $gift->points]);
    }
}
