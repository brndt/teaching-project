<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Enum;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidRoleException;

final class Role extends Enum
{
    public const STUDENT = 'student';
    public const TEACHER = 'teacher';
    public const ADMIN = 'admin';
    private string $role;

    public function toString(): string
    {
        return $this->value;
    }

    protected function throwExceptionForInvalidValue($value)
    {
        throw new InvalidRoleException();
    }
}