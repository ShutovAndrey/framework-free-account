<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exception\UnauthenticatedException;
use App\Exception\ValidationException;
use App\Factory\GiftProcessorFactory;
use App\Models\Gift;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ConfirmGiftAction extends Action
{
    protected Response $response;

    protected Capsule $db;

    protected GiftProcessorFactory $giftFactory;

    public function __construct(Response $response, Capsule $db, GiftProcessorFactory $giftFactory)
    {
        parent::__construct($response, $db);
        $this->giftFactory = $giftFactory;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function action(): Response
    {
        if (!$id = (int) $this->getAttribute('id')) {
            throw new ValidationException('Please provide a valid gift id');
        }
        /** @var Gift $gift */
        $gift = Gift::firstWhere('id', $id);

        if (empty($gift)) {
            throw new UnauthenticatedException();
        }

        $gift->update(['confirmed' => true]);
        if (null !== $user = User::firstWhere('id', $this->uid)) {
            $this->giftFactory->getProcessor($gift->type)->process($user, $gift);
        }

        return $this->respond();
    }
}
