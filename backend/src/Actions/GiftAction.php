<?php

declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use App\Models\{GoodsStore, Gift, Settings};
use App\Enums\GiftType;

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

        $giftAttributes = match ($type) {
            GiftType::CACHE => $this->processPayment(),
            GiftType::POINTS => $this->processPoints(),
            GiftType::GOOD => $this->processGoods(),
        };

        if (empty($giftAttributes)) {
            $giftAttributes = $this->processPoints();
        }

        $attributes += $giftAttributes;

        $gift = new Gift();
        $gift->fill($attributes);
        $gift->save();

        return $this->respond($gift->toArray());
    }

    protected function getType(): GiftType
    {
        $types = GiftType::cases();
        shuffle($types);

        return $types[0];
    }

    protected function processPayment(): array
    {
        $limitNotReached = Settings::whereColumn('cache_fund', '>=', 'cache_max')->exists();
        if ($limitNotReached) {
            return [
                'type' => GiftType::CACHE->value,
                'amount' => $this->getRandomValue(Settings::first('cache_max')->cache_max),
            ];
        } else {
            return [];
        }
    }

    protected function processPoints(): array
    {
        return [
            'type' => GiftType::POINTS->value,
            'points' => $this->getRandomValue(Settings::first('points_max')->points_max),
        ];
    }

    protected function processGoods(): array
    {
        $good = GoodsStore::where('quantity', '>', '0')
            ->leftJoin('goods', 'goods.id', '=', 'goods_store.good_id')
            ->where('gift', true)->get('good_id')->random();

        if ($good) {
            return [
                'type' => GiftType::GOOD->value,
                'good_id' => $good->good_id,
            ];
        } else {
            return [];
        }
    }

    private function getRandomValue(int $max): int
    {
        return rand(0, $max);
    }
}
