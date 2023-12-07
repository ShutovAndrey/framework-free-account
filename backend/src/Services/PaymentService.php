<?php

declare(strict_types=1);

namespace App\Services;

use App\Factory\LoggerFactory;
use App\Models\PaymentData;
use App\Models\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Illuminate\Database\Capsule\Manager as Capsule;

class PaymentService
{
    protected Capsule $db;

    protected \Psr\Log\LoggerInterface $logger;

    private Client $http;

    private array $config;

    public function __construct(
        Capsule $db,
        LoggerFactory $logger,
        array $config
    ) {
        $this->db = $db;
        $this->logger = $logger->createInstance();
        $this->config = $config;
    }

    public function create(PaymentData $data): void
    {
        $this->db::table('settings')->decrement('cache_fund', $data->amount);

        $this->initializeHttp();

        $response = $this->http->post('anything', [
            'amount' => $data->amount,
            'account' => $data->bankAccout,
        ]);

        $body = \json_decode((string) $response->getBody(), true);

        if (200 === $response->getStatusCode()) {
            if ($body && $uuid = \mb_substr($body['headers']['X-Amzn-Trace-Id'], 7, 36)) {
                $transaction = new Transaction();
                $transaction->user_id = $data->userId;
                $transaction->uuid = $uuid;
                $transaction->amount = $data->amount;
                $transaction->save();
            }
            $this->logger->info(__METHOD__ . ' RESPONSE', $body);
        } else {
            $this->logger->error("Cant proccess payment for {$data->userId}. Gift id: {$data->giftId}");
        }
    }

    private function initializeHttp(): void
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $token = $this->config['bank']['apiKey'];

        $this->http = new Client([
            'handler' => $stack,
            'base_uri' => 'https://httpbin.org/',
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'TestPayment/1.0',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }
}
