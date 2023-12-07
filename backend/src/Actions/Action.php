<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use PSR\Http\Message\ServerRequestInterface as Request;

abstract class Action
{
    protected Request $request;

    protected Response $response;

    protected array $body = [];

    protected Capsule $db;

    protected ?int $uid;

    public function __construct(Response $response, Capsule $db)
    {
        $this->response = $response;
        $this->db = $db;
    }

    public function __invoke(Request $request): Response
    {
        $this->request = $request;
        $this->uid = $this->getAttribute('uid');

        return $this->action();
    }

    abstract protected function action(): Response;

    protected function getFormData(): array
    {
        return $this->body = (array) $this->request->getParsedBody();
    }

    /**
     * @throws \JsonException
     */
    protected function getBody(): array
    {
        if ($this->body) {
            return $this->body;
        }

        if (!$input = \file_get_contents('php://input')) {
            return $this->getFormData();
        }
        $input = \json_decode($input, true, 512, JSON_THROW_ON_ERROR);

        return $this->body = $input;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    protected function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->request->getAttribute($name, $default);
    }

    protected function respond(array | int $payload = [], int $status = 200): Response
    {
        if ($payload) {
            $json = \json_encode($payload);
            $this->response->getBody()->write($json);
        }

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status)
        ;
    }
}
