<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\Exception;

use Exception;
use Throwable;

final class StudentAnswerAlreadyExists extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Student answer already exists'), $code, $previous);
    }
}
