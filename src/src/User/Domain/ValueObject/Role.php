<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Enum;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidRoleException;

final class Role extends Enum
{
    private string $role;

    public const STUDENT = 'student';
    public const TEACHER = 'teacher';
    public const ADMIN = 'admin';

    protected function throwExceptionForInvalidValue($value)
    {
        throw new InvalidRoleException();
    }

    public function toString(): string {
        return $this->value;
    }
}