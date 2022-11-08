<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class UnauthorizedException extends RuntimeException
{
    /**
     * @var string
     */
    protected $message = 'Session expired';

    /**
     * @var int
     */
    protected $code = 401;
}
