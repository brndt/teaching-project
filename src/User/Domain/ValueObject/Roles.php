<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidRoleException;
use Stringable;

final class Roles implements Stringable
{
    private array $roles;

    private function __construct(Role ...$roles)
    {
        $this->setValue(...$roles);
    }

    private function setValue(Role ...$roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @throws InvalidRoleException
     */
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

    public static function fromArrayOfRole(Role ...$roles)
    {
        return new self(...$roles);
    }

    public function toArrayOfRole(): array
    {
        return $this->roles;
    }

    public function __toString(): string
    {
        return json_encode($this->getArrayOfPrimitives());
    }

    public function getArrayOfPrimitives(): array
    {
        return array_map($this->roleToPrimitive(), $this->roles);
    }

    private function roleToPrimitive(): callable
    {
        return static function (Role $role) {
            return $role->toString();
        };
    }

    public function ensureRolesDontContainsAdmin(): void
    {
        if ($this->contains(new Role(Role::ADMIN))) {
            throw new PermissionDeniedException();
        }
    }

    public function contains(Role $role): bool
    {
        return in_array($role->toString(), $this->getArrayOfPrimitives());
    }

}
