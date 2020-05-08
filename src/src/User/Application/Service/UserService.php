<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidLetterContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidPasswordLengthException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidRoleException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;

abstract class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    protected function createPasswordFromPrimitive(string $password): Password
    {
        try {
            return Password::fromPlainPassword($password);
        } catch (InvalidPasswordLengthException | InvalidNumberContainingException | InvalidLetterContainingException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }
    }

    protected function createEmailFromPrimitive(string $email): Email
    {
        try {
            return new Email($email);
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }
    }

    protected function createRolesFromPrimitive(array $roles): Roles
    {
        try {
            return Roles::fromArrayOfPrimitives($roles);
        } catch (InvalidRoleException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }
    }

    protected function ensureUserDoesntExistByEmail(string $email): void
    {
        $email = $this->createEmailFromPrimitive($email);
        if (null !== $this->userRepository->ofEmail($email)) {
            throw new UserAlreadyExistsException();
        }
    }

    protected function ensureUserExists(?User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    protected function ensureUsersExist(?array $users): void
    {
        if (true === empty($users)) {
            throw new UserNotFoundException();
        }
    }

    protected function ensureUserEnabled(User $user): void
    {
        if (true === $user->getEnabled()) {
            throw new UserAlreadyEnabledException();
        }
    }
}