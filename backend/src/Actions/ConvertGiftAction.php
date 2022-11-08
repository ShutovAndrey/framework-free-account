<?php

declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use App\Models\{Gift, User};
use App\Enums\GiftType;

class ConvertGiftAction extends Action
{
    protected function action(): Response
    {
        $id = (int) $this->getAttribute('id');

        if (!$id) {
            throw new \App\Exception\ValidationException('Please provide a valid gift id');
        }

        $gift = Gift::firstWhere([['id', $id], ['type', GiftType::CACHE->value]]);

        if (empty($gift)) {
            throw new \App\Exception\UnauthenticatedException();
        }
        $user = User::firstWhere('id', $this->uid);
        $points = round($gift->amount * $user->rate);

        $gift->delete();

        $attributes = [
            'user_id' => $this->uid,
            'type' => GiftType::POINTS->value,
            'points' => $points,
            'confirmed' => true,
        ];

        $newGift = new Gift();
        $newGift->fill($attributes);
        $newGift->save();

        $user->update(['points' => $user->points + $points]);

        return $this->respond($newGift->toArray());
    }
}
