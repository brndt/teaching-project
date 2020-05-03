<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Permissions;

use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserPermissions extends Voter
{
    const EDIT = 'edit';

    protected function supports($attribute, $userToUpdate)
    {
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }
        if (!$userToUpdate instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $userToUpdate, TokenInterface $token)
    {
        $executingUser = $token->getUser();

        if (!$executingUser instanceof SymfonyUser) {
            return false;
        }

        return $this->canEdit($userToUpdate, $executingUser);
    }

    private function canEdit(User $userToUpdate, SymfonyUser $executingUser)
    {
        return $executingUser->getId() === $userToUpdate->getId()->toString();
    }
}