<?php

namespace App\Exceptions;

use RuntimeException;

class ExternalServiceException extends RuntimeException
{
    public function __construct(
        public string $service,
        public ?int $statusCode = null,
        string $message = '',
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message ?: "{$service} request failed", 0, $previous);
    }
}
