<?php declare(strict_types=1);

namespace Todo\Exception;

use RuntimeException;

class TodoInvalidDataException extends RuntimeException
{
    public function __construct(string $message = "Invalid data", int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
