<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

final class AuthorizationService
{
    public function ensureRequestAuthorIsCertainUser(User $requestAuthor, User $certainUser)
    {
        if (false === $requestAuthor->idEqualsTo($certainUser->getId())) {
            throw new PermissionDeniedException();
        }
    }

    public function ensureRequestAuthorIsOneOfUsers(User $author, User $firstUser, User $secondUser): void
    {
        if (false === $author->idEqualsTo($firstUser->getId()) && false === $author->idEqualsTo($secondUser->getId())) {
            throw new PermissionDeniedException();
        }
    }

    public function ensureRequestAuthorHasPermissionsToUserConnection(User $author, User $user): void
    {
        if (false === $author->isInRole(new Role('admin')) && false === $author->idEqualsTo($user->getId())) {
            throw new PermissionDeniedException();
        }
    }
}
