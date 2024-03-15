<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class SameDataException extends HttpException
{
    public function __construct(
        int $code = 405,
        int $statusCode = 405,
        string $message = "Данные идентичны, обновление не требуется",
        Throwable $previous = null,
        array $headers = []
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }


}
