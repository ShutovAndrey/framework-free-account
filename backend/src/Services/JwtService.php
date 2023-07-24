<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\{ExpiredException, Key, JWT};
use UnexpectedValueException;
use DomainException;

class JwtService
{
    private int $lifetime;
    private string $key;

    public function __construct(array $settings)
    {
        $this->lifetime = (int) $settings['lifetime'];
        $this->key = (string) $settings['key'];
    }

    public function createJwt(array $payload, int $lifetime = 0): string
    {
        if ($lifetime > 0) {
            $this->lifetime = $lifetime;
        }

        $expiresOn = time() + $this->lifetime;
        $payload += ['exp' => $expiresOn];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    public function getClaim(string $name, $default = null)
    {
        return $this->_tokenDecoded[$name] ?? $default;
    }

    public function validateToken(string $accessToken): bool
    {
        $key = new Key($this->key, 'HS256');

        try {
            $this->_tokenDecoded = (array) JWT::decode($accessToken, $key);
        } catch (ExpiredException $e) {
            return false;
        } catch (UnexpectedValueException $e) {
            return false;
        } catch (DomainException $e) {
            return false;
        }
        return true;
    }
}
