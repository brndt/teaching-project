<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Domain\Exception;

use Exception;
use Throwable;

final class InvalidEmailException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Email address is invalid'), $code, $previous);
    }
}