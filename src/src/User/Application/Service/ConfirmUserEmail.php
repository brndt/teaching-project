<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;

final class ConfirmUserEmail extends UserService
{

    public function __invoke(ConfirmUserEmailRequest $request)
    {
        $user = $this->userRepository->ofId($this->createIdFromPrimitive($request->getUserId()));
        $this->ensureUserExists($user);
        $this->ensureConfirmationTokenExists($user->getConfirmationToken());

        $this->validateConfirmationTokenFromUser(
            $request->getConfirmationToken(),
            $user->getConfirmationToken()->toString()
        );

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $this->userRepository->save($user);
    }

    private function ensureConfirmationTokenExists(?Token $tokenFromUser)
    {
        if (null == $tokenFromUser) {
            throw new ConfirmationTokenNotFoundException();
        }
    }

    private function validateConfirmationTokenFromUser(string $tokenFromRequest, string $tokenFromUser)
    {
        if ($tokenFromRequest !== $tokenFromUser) {
            throw new IncorrectConfirmationTokenException();
        }
    }
}