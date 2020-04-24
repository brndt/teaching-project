<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Application\Exception;

use Throwable;

final class InvalidArgumentValidationException extends \Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($message), $code, $previous);
    }
}