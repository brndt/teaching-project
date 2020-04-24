<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Exception;

use Exception;
use Throwable;

final class UserAlreadyExistsException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('User is already exists'), $code, $previous);
    }
}