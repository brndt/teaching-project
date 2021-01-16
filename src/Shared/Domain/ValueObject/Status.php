<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\ValueObject;

use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidStatusException;

final class Status extends Enum
{
    public const PUBLISHED = 'published';
    public const UNPUBLISHED = 'unpublished';
    public const ARCHIVE = 'archive';

    protected function throwExceptionForInvalidValue($value)
    {
        throw new InvalidStatusException('Invalid status name');
    }
}