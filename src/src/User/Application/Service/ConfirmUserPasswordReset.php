<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserPasswordResetRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidLetterContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidPasswordLengthException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;

final class ConfirmUserPasswordReset
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(ConfirmUserPasswordResetRequest $request)
    {
        $user = $this->userRepository->ofId($this->createIdFromPrimitive($request->getUserId()));
        $this->checkIfUserExists($user);
        $this->checkIfConfirmationTokenExists($user->getConfirmationToken());

        $this->validateConfirmationTokenFromUser(
            $request->getConfirmationToken(),
            $user->getConfirmationToken()->toString()
        );

        $user->setConfirmationToken(null);
        $user->setPassword($this->createPasswordFromPrimitive($request->getNewPassword()));

        $this->userRepository->save($user);
    }

    private function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    private function checkIfUserExists(?User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    private function checkIfConfirmationTokenExists(?Token $tokenFromUser)
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

    private function createPasswordFromPrimitive(string $password): Password
    {
        try {
            return Password::fromPlainPassword($password);
        } catch (InvalidPasswordLengthException | InvalidNumberContainingException | InvalidLetterContainingException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }
    }
}