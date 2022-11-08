<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Throwable;

final class ValidationException extends RuntimeException
{
    /**
     * @var array|string|null
     */
    private $errors;

    public function __construct(
        $errors = null,
        string $message = 'Please fix validation errors',
        int $code = 422,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * @return array|string|null
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
