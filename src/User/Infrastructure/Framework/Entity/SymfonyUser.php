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

    public function getRoles()
    {
        return parent::getRoles()->toPrimitives();
    }

    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->getEmail();
    }
}