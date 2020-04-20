<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Exception;

use Exception;
use Throwable;

final class RefreshTokenIsExpiredException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Refresh Token is expired'), $code, $previous);
    }
}