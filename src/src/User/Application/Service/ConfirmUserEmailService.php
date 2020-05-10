<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class ConfirmUserEmailService extends UserService
{
    public function __invoke(ConfirmUserEmailRequest $request)
    {
        $userId = $this->createIdFromPrimitive($request->getUserId());
        $confirmationToken = new Token($request->getConfirmationToken());

        $user = $this->userRepository->ofId($userId);
        $this->ensureUserExists($user);

        $this->validateConfirmationTokenFromRequest($user, $confirmationToken);

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $this->userRepository->save($user);
    }
}