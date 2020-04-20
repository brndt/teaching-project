<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

final class Roles
{
    private array $roles;

    public function __construct(Role ...$roles)
    {
        $this->roles = $roles;
    }

    public function getValue(): array
    {
        return $this->roles;
    }

    public function toPrimitives()
    {
        return array_map($this->roleToPrimitive(), $this->roles);
    }

    private function roleToPrimitive(): callable
    {
        return static function (Role $role) {
            return $role->getValue();
        };
    }

    public static function fromPrimitives($roles): Roles
    {
        return new Roles(
            ...
            array_map(
                function ($role) {
                    return new Role($role);
                },
                $roles
            )
        );
    }

}