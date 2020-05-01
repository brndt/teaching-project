<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\CheckPermission;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidLetterContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidPasswordLengthException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;

final class UpdateUserPassword
{
    private UserRepository $userRepository;
    private CheckPermission $security;

    public function __construct(UserRepository $userRepository, CheckPermission $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function __invoke(UpdateUserPasswordRequest $request): void
    {
        $userToUpdate = $this->userRepository->ofId($this->createIdFromPrimitive($request->getId()));

        if (false === $this->security->isGranted('edit', $userToUpdate)) {
            throw new PermissionDeniedException();
        }

        $this->checkIfUserExists($userToUpdate);

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

    private function checkIfUserExists(?User $user): void
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