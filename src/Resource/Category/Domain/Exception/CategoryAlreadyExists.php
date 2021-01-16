<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Domain\Exception;

use Exception;
use Throwable;

final class CategoryAlreadyExists extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Category already exists'), $code, $previous);
    }
}