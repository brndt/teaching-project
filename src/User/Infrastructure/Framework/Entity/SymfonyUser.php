<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity;

use LaSalle\StudentTeacher\User\Domain\User;
use Symfony\Component\Security\Core\User\UserInterface;

final class SymfonyUser extends User implements UserInterface
{
    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return array_map($this->processValueToSymfonyRole(), parent::getRoles()->toArrayOfPrimitives());
    }

    public function getUsername()
    {
        return $this->getEmail()->toPrimitives();
    }

    private function processValueToSymfonyRole() {
        return static function (string $role) {
            return strtoupper('ROLE_'.$role);
        };
    }
}