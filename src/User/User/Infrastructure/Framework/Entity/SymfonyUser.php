<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Infrastructure\Framework\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

final class SymfonyUser implements UserInterface
{
    public function __construct(
        private string $id,
        private string $username,
        private string $password,
        private array $roles,
        private bool $enabled
    ) {
    }

    public static function processValueToSymfonyRole(array $roles): array
    {
        return array_map(
            static function (string $role) {
                return strtoupper('ROLE_' . $role);
            },
            $roles
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}