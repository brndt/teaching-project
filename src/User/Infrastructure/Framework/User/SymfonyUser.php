<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\User;

use LaSalle\StudentTeacher\User\Domain\User;
use Symfony\Component\Security\Core\User\UserInterface;

final class SymfonyUser extends User implements UserInterface
{
    public function getRoles()
    {
        return [$this->getRole()];
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
    }
}