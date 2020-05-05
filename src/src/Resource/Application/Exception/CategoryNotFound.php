<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Exception;

use Exception;
use Throwable;

final class CategoryNotFound extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Category not found'), $code, $previous);
    }
}