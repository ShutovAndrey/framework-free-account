<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;

class AccountAction extends Action
{
    protected function action(): Response
    {
        $user = User::firstWhere('id', $this->uid);
        return $this->respond($user->toArray());
    }
}
