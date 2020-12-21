<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Exception;

use Exception;
use Throwable;

final class IncorrectStateException extends Exception
{
    public function __construct(string $message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('New state is incorrect'), $code, $previous);
    }
}
