<?php

namespace App\Interfaces;

use App\Models\Gift;
use App\Models\User;

interface GiftProcessorInterface
{
    public function process(User $user, Gift $gift): void;
}
