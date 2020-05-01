<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Application\Exception;

use Exception;
use Throwable;

final class PermissionDeniedException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('You do not have permission to perform this action', $code, $previous);
    }
}