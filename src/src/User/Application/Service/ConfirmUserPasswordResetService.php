<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserPasswordResetRequest;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class ConfirmUserPasswordResetService extends UserService
{
    public function __invoke(ConfirmUserPasswordResetRequest $request)
    {
        $userId = $this->createIdFromPrimitive($request->getUserId());
        $confirmationToken = new Token($request->getConfirmationToken());
        $newPassword = $this->createPasswordFromPrimitive($request->getNewPassword());

        $user = $this->userRepository->ofId($userId);
        $this->ensureUserExists($user);

        $this->validateConfirmationTokenFromRequest($user, $confirmationToken);

        $user->setConfirmationToken(null);
        $user->setPassword($newPassword);

        $this->userRepository->save($user);
    }
}