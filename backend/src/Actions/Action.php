<?php

declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use PSR\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class Action
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var array
     */
    protected array $body = [];

    protected $db;

    /**
     * @var int
     */
    protected $uid;

    public function __construct(Response $response, Capsule $db)
    {
        $this->response = $response;
        $this->db = $db;
    }

    /**
     * @param Request  $request
     * @param array    $args
     * @return Response
     */
    public function __invoke(Request $request, $args = []): Response
    {
        $this->request = $request;
        $this->args = $args;

        $this->uid = $this->getAttribute('uid');

        return $this->action();
    }

    /**
     * @return Response
     */
    abstract protected function action(): Response;

    /**
     * @return array
     */
    protected function getFormData(): array
    {
        return $this->body = (array) $this->request->getParsedBody();
    }

    /**
     * @return array
     */
    protected function getBody(): array
    {
        if ($this->body) {
            return $this->body;
        }

        if (!$input = file_get_contents('php://input')) {
            return $this->getFormData();
        }
        $input = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE && !$input = $this->getFormData()) {
            throw new HttpBadRequestException($this->request, 'Invalid JSON input format.');
        }

        return $this->body = $input;
    }

    /**
     * Get body value by key
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function input(string $key, $default = null)
    {
        return isset($this->body[$key]) ? $this->body[$key] : $default;
    }

    /**
     * @return mixed
     */
    protected function getAttribute($name, $default = null)
    {
        return $this->request->getAttribute($name, $default);
    }

    /**
     * @param array|int $payload
     * @param int $status
     * @return Response
     */
    protected function respond(array|int $payload = [], int $status = 200): Response
    {
        if ($payload) {
            $json = json_encode($payload);
            $this->response->getBody()->write($json);
        }

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
