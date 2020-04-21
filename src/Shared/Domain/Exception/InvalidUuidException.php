<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\Exception;

use Exception;
use Throwable;

final class InvalidUuidException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Uuid is invalid'), $code, $previous);
    }
}