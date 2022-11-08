<?php

declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Gift;

class RefuseGiftAction extends Action
{
    protected function action(): Response
    {
        $id = (int) $this->getAttribute('id');

        if (!$id) {
            throw new \App\Exception\ValidationException('Please provide a valid gift id');
        }
        $gift = Gift::firstWhere('id', $id);

        if (empty($gift)) {
            throw new \App\Exception\UnauthenticatedException();
        }

        $gift->delete();

        return $this->respond($gift->id);
    }
}
