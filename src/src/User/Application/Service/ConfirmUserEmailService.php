<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class ConfirmUserEmailService extends UserService
{
    public function __invoke(ConfirmUserEmailRequest $request): void
    {
        $userId = $this->createIdFromPrimitive($request->getUserId());
        $user = $this->userRepository->ofId($userId);
        $this->ensureUserExists($user);

        $confirmationToken = new Token($request->getConfirmationToken());
        $this->validateConfirmationToken($user, $confirmationToken);

        $user->setConfirmationToken(null);
        $user->setExpirationDate(null);
        $user->setEnabled(true);

        $this->userRepository->save($user);
    }
}