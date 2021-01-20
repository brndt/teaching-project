<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Shared\Application\Exception;

use Exception;
use Throwable;

final class UserAlreadyEnabledException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('User already has been enabled'), $code, $previous);
    }
}