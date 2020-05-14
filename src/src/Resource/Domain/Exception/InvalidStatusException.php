<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Exception;

use Exception;
use Throwable;

final class InvalidStatusException extends Exception
{
    public function __construct(string $message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}