<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use App\Services\JwtService;
use Illuminate\Database\Capsule\Manager as Capsule;

class TokenCreateAction extends Action
{
    protected Capsule $db;

    protected JwtService $jwt;

    protected Response $response;

    public function __construct(
        Response $response,
        Capsule $db,
        JwtService $jwt
    ) {
        parent::__construct($response, $db);
        $this->jwt = $jwt;
    }

    /**
     * @throws \JsonException
     */
    protected function action(): Response
    {
        $validator = new \Valitron\Validator($this->getBody(), ['email', 'password']);
        $validator->mapFieldsRules([
            'password' => [['required']],
            'email' => ['required', 'email'],
        ]);

        if (!$validator->validate()) {
            throw new \App\Exception\ValidationException($validator->errors());
        }

        $user = User::firstWhere('email', mb_strtolower(trim($this->input('email'))));

        if (empty($user)) {
            throw new \App\Exception\UnauthenticatedException();
        }

        if (!password_verify((string) $this->input('password'), $user->password)) {
            throw new \App\Exception\UnauthenticatedException();
        }

        $payload = [
            'uid' => $user->id,
            'role' => 'user',
            'action' => 'auth:access',
        ];

        $token = $this->jwt->createJwt($payload);

        $response = [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];

        return $this->respond($response, 201);
    }
}
