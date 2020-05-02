<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Exception;

use Throwable;

final class RoleIsNotStudentOrTeacherException extends \Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('User role is incorrect'), $code, $previous);
    }
}