<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Exception;

use Exception;
use Throwable;

final class InvalidLetterContainingException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Password doesn\'t contain any letter characters'), $code, $previous);
    }
}