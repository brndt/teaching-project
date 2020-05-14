<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Exception;

use Exception;
use Throwable;

final class InvalidNameException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Name should only contain alpha characters or a hyphen or apostrophe'), $code, $previous);
    }
}