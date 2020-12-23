<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Exception;

use Exception;
use Throwable;

final class CoursePermissionAlreadyExists extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Course permission already exists'), $code, $previous);
    }
}
