<?php

declare(strict_types=1);

namespace App\Factory;

use App\Enums\GiftType;
use App\Interfaces\GiftProcessorInterface;
use App\Services\GiftProcessor\CacheGiftProcessor;
use App\Services\GiftProcessor\GoodsGiftProcessor;
use App\Services\GiftProcessor\PointsGiftProcessor;
use App\Services\PaymentService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class GiftProcessorFactory
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @throws \Exception
     */
    public function getProcessor(int $type): GiftProcessorInterface
    {
        return match (GiftType::from($type)) {
            GiftType::CACHE => new CacheGiftProcessor($this->paymentService),
            GiftType::POINTS => new PointsGiftProcessor(),
            GiftType::GOOD => new GoodsGiftProcessor(),
        };
    }
}
