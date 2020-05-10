<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\ValueObject;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Enum;

final class Status extends Enum
{
    public const PUBLISHED = 'published';
    public const UNPUBLISHED = 'unpublished';
    public const DRAFT = 'draft';

    protected function throwExceptionForInvalidValue($value)
    {
        throw new InvalidArgumentException($value);
    }
}