<?php

declare(strict_types=1);

namespace App\Models;

class PaymentData
{
    public $userId;
    public $giftId;
    public $amount;
    public $bankAccout;

    public function __construct(int $userId, int $giftId, int $amount, string $bankAccout)
    {
        $this->userId = $userId;
        $this->giftId = $giftId;
        $this->amount = $amount;
        $this->bankAccout = $bankAccout;
    }
}
