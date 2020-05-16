<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserPasswordResetRequest;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class ConfirmUserPasswordResetService extends UserService
{
    public function __invoke(ConfirmUserPasswordResetRequest $request): void
    {
        $userId = $this->createIdFromPrimitive($request->getUserId());
        $user = $this->userRepository->ofId($userId);
        $this->ensureUserExists($user);

        $confirmationToken = new Token($request->getConfirmationToken());
        $this->validateConfirmationToken($user, $confirmationToken);

        $newPassword = $this->createPasswordFromPrimitive($request->getNewPassword());

        $user->setConfirmationToken(null);
        $user->setExpirationDate(null);
        $user->setPassword($newPassword);

        $this->userRepository->save($user);
    }
}