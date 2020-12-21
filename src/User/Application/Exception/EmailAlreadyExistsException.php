<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Exception;

use Exception;
use Throwable;

final class EmailAlreadyExistsException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Email already in use'), $code, $previous);
    }
}
