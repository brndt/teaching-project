<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\ValueObject;

use LaSalle\StudentTeacher\Resource\Domain\Exception\InvalidStatusException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Enum;

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