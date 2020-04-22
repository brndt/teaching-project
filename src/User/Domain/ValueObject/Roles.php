<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

final class Roles
{
    private array $roles;

    public static function fromArrayOfPrimitives(array $roles): Roles
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

    public static function fromArrayOfRole(Role...$roles)
    {
        return new self($roles);
    }

    public function toArrayOfPrimitives(): array
    {
        return array_map($this->roleToPrimitive(), $this->roles);
    }

    public function toArrayOfRole(): array
    {
        return $this->roles;
    }

    public function __toString(): string
    {
        return json_encode($this->toArrayOfPrimitives());
    }

    private function __construct(Role ...$roles)
    {
        $this->setValue(...$roles);
    }

    private function setValue(Role ...$roles): void
    {
        $this->roles = $roles;
    }

    private function roleToPrimitive(): callable
    {
        return static function (Role $role) {
            return $role->toString();
        };
    }

}