<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

final class SymfonyUser implements UserInterface
{
    private string $id;
    private string $username;
    private string $password;
    private array $roles;

    public function __construct(string $id, string $username, string $password, array $roles)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
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

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
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
}