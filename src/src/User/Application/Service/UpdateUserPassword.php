<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Application\Exception\OldPasswordIncorrectException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidLetterContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidPasswordLengthException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;

final class UpdateUserPassword
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(UpdateUserPasswordRequest $request): void
    {
        $userToUpdate = $this->userRepository->ofId($this->createIdFromPrimitive($request->getId()));

        $this->checkIfExists($userToUpdate);

        $this->verifyOldPassword($request->getOldPassword(), $userToUpdate->getPassword());

        $userToUpdate->setPassword($this->createPasswordFromPrimitive($request->getNewPassword()));

        $this->userRepository->save($userToUpdate);
    }

    private function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    private function checkIfExists(User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    private function verifyOldPassword(string $oldPassword, Password $userPassword): void
    {
        if (false === Password::verify($oldPassword, $userPassword)) {
            throw new IncorrectPasswordException();
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