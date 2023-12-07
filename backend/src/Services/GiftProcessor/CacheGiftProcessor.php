<?php

declare(strict_types=1);

namespace App\Services\GiftProcessor;

use App\Interfaces\GiftProcessorInterface;
use App\Models\Gift;
use App\Models\PaymentData;
use App\Models\User;
use App\Services\PaymentService;

final class CacheGiftProcessor implements GiftProcessorInterface
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function process(User $user, Gift $gift): void
    {
        $this->paymentService->create(new PaymentData(
            $user->id,
            $gift->id,
            $gift->amount,
            $user->bank_account
        ));
    }
}
