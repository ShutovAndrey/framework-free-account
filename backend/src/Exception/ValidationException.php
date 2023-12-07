<?php

declare(strict_types=1);

namespace App\Exception;

final class ValidationException extends \RuntimeException
{
    /**
     * @var null|array|string
     */
    private $errors;

    public function __construct(
        $errors = null,
        string $message = 'Please fix validation errors',
        int $code = 422,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * @return null|array|string
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
