<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Exception;

use Exception;
use Throwable;

final class CourseNotFoundException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Course not found'), $code, $previous);
    }
}