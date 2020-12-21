<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Exception;

use Exception;
use Throwable;

final class ResourceStudentAnswerNotFoundException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Resource student answer not found'), $code, $previous);
    }
}
