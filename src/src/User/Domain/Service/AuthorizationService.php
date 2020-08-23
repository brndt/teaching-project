<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

final class AuthorizationService
{
    public function ensureRequestAuthorIsCertainUser(User $requestAuthor, User $certainUser) {
        if (false === $requestAuthor->idEqualsTo($certainUser->getId())) {
            throw new PermissionDeniedException();
        }
    }
}
