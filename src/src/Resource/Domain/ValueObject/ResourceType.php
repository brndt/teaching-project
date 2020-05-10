<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\ValueObject;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Enum;

final class ResourceType extends Enum
{
    public const THEORY  = 'theory';
    public const EXAM = 'exam';
    public const EXERCISE = 'exercise';

    protected function throwExceptionForInvalidValue($value)
    {
        throw new InvalidArgumentException($value);
    }
}