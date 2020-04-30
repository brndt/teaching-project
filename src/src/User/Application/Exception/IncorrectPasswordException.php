<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Exception;

use Exception;
use Throwable;

final class IncorrectPasswordException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Your password is incorrect'), $code, $previous);
    }
}