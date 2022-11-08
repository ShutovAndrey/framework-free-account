<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class UnauthenticatedException extends RuntimeException
{
    /**
     * @var string
     */
    protected $message = 'Invalid credentials';

    /**
     * @var int
     */
    protected $code = 401;
}
