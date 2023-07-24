<?php

declare(strict_types=1);

namespace App\Models;

class PaymentData
{
    public int $userId;
    public int $giftId;
    public int $amount;
    public string $bankAccout;

    public function __construct(int $userId, int $giftId, int $amount, string $bankAccout)
    {
        $this->userId = $userId;
        $this->giftId = $giftId;
        $this->amount = $amount;
        $this->bankAccout = $bankAccout;
    }
}
