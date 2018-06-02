<?php declare(strict_types=1);

namespace Todo\Exception;

use RuntimeException;

class TodoNotFoundException extends RuntimeException
{
    public function __construct(string $message = "Not Found", int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
