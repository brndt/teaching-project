<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Exception;

use Exception;
use Throwable;

final class UnitAlreadyExists extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Unit already exists'), $code, $previous);
    }
}
