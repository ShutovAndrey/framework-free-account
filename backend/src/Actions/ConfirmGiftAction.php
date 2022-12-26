<?php

declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Services\PaymentService;
use App\Models\{
    User,
    GoodsStore,
    Delivery,
    Gift,
    PaymentData
};
use App\Enums\{GiftType, DeliveryStatus};


class ConfirmGiftAction extends Action
{
    protected $response;
    protected $db;
    protected $payment;
    protected $user;

    public function __construct(Response $response, Capsule $db, PaymentService $payment) {
        $this->response = $response;
        $this->db = $db;
        $this->payment = $payment;
    }

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

        $gift->update(['confirmed' => true]);

        $this->user = User::firstWhere('id', $this->uid);
        $this->processGift($gift);

        return $this->respond();
    }

    protected function processGift(Gift $gift): void
    {
        match (GiftType::from($gift->type)) {
            GiftType::CACHE =>  $this->processPayment($gift),
            GiftType::GOOD =>  $this->processPost($gift),
            GiftType::POINTS => $this->processPoints($gift),
        };
    }

    protected function processPayment(Gift $gift): void
    {

        $this->payment->create(new PaymentData(
            $this->user->id,
            $gift->id,
            $gift->amount,
            $this->user->bank_account
        ));
    }

    protected function processPost(Gift $gift): void
    {
        $store = GoodsStore::firstWhere('good_id', $gift->good_id);
        $store->decrement('quantity');

        $delivery = new Delivery();
        $delivery->good_id = $gift->good_id;
        $delivery->user_id = $gift->user_id;
        $delivery->address = $this->user->address;
        $delivery->status = DeliveryStatus::SENDING;
        $delivery->save();
    }

    protected function processPoints(Gift $gift): void
    {
        $user = User::firstWhere('id', $this->uid);
        $user->update(['points' => $user->points + $gift->points]);
    }
}
