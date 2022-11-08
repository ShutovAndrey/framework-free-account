<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\GiftType;

class Gift extends Model
{
    use SoftDeletes;

    protected $table = 'users_gifts';

    protected $fillable = [
        'user_id',
        'good_id',
        'points',
        'amount',
        'confirmed',
        'type',
    ];

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
        ];

        return $data += match (GiftType::from($this->type)) {
            GiftType::CACHE =>  ['amount' => $this->amount],
            GiftType::POINTS =>  ['points' => $this->points],
            GiftType::GOOD =>  ['good' => Good::whereId($this->good_id)->first('name')->name],
        };
    }
}
