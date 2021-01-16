<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Exception;

use Exception;
use Throwable;

final class CoursePermissionNotFound extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Course permission not found'), $code, $previous);
    }
}
