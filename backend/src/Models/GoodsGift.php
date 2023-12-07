<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GiftType;
use App\Interfaces\GiftInterface;

class GoodsGift extends Gift implements GiftInterface
{
    public static function isAvailable(): bool
    {
        return GoodsStore::where('quantity', '>', '0')
            ->where('is_gift', true)->exists()
        ;
    }

    public function getAttributes(): array
    {
        $good = GoodsStore::where('quantity', '>', '0')
            ->leftJoin('goods', 'goods.id', '=', 'goods_store.good_id')
            ->where('gift', true)->get('good_id')->random()
        ;

        return [
            'type' => GiftType::GOOD->value,
            'good_id' => $good->good_id,
        ];
    }
}
