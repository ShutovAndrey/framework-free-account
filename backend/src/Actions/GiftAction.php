<?php

declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Gift;
use App\Interfaces\GiftInterface;
use App\Factory\GiftFactory;

class GiftAction extends Action
{
    protected function action(): Response
    {
        if ($gift = Gift::firstWhere([['user_id', $this->uid]])) {
            if ($gift->confirmed) {
                throw new \App\Exception\ValidationException(null, 'User allready have a gift');
            } else {
                return $this->respond($gift->toArray());
            }
        }

        $attributes = ['user_id' => $this->uid];
        $type = $this->getType();

        $attributes += $type->getAttributes();

        $gift = new Gift();
        $gift->fill($attributes);
        $gift->save();

        return $this->respond($gift->toArray());
    }

    protected function getType(): GiftInterface
    {
        $factory = new GiftFactory();
        return $factory->getRandomGift();
    }
}
