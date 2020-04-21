<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

use LaSalle\StudentTeacher\User\Domain\Exception\InvalidRoleException;

final class Role
{
    private string $role;

    private const STUDENT = 'student';
    private const TEACHER = 'teacher';
    private const ADMIN = 'admin';

    public function __construct(string $role)
    {
        $this->setValue($role);
    }

    public function toPrimitives(): string
    {
        return $this->role;
    }

    public function __toString()
    {
        return $this->role;
    }

    public static function ArrayRoles(): array
    {
        return [
            self::STUDENT,
            self::TEACHER,
            self::ADMIN
        ];
    }

    private function setValue(string $role)
    {
        $this->assertValueInArray($role);
        $this->role = $role;
    }

    private function assertValueInArray(string $role): void
    {
        if (!in_array($role, $this->ArrayRoles())) {
            throw new InvalidRoleException();
        }
    }
}