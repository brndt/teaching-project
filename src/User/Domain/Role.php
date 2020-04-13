<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

final class Role
{
    private string $value;

    private const STUDENT = 'ROLE_STUDENT';
    private const TEACHER = 'ROLE_TEACHER';
    private const ADMIN = 'ROLE_ADMIN';

    public function __construct(string $value)
    {
        $this->setValue($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function ArrayRoles(): array
    {
        return [
            self::STUDENT,
            self::TEACHER,
            self::ADMIN
        ];
    }

    public function setValue(string $role)
    {
        if (!in_array($role, $this->ArrayRoles())) {
            throw new \Error('Invalid Role parameter');
        }
        $this->value = $role;
    }

    public function __toString()
    {
        return $this->value;
    }
}