<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidLetterContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNameException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidPasswordLengthException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidRoleException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

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
            throw new InvalidArgumentException($error->getMessage());
        }
    }

    protected function createPasswordFromPrimitive(string $password): Password
    {
        try {
            return Password::fromPlainPassword($password);
        } catch (InvalidPasswordLengthException | InvalidNumberContainingException | InvalidLetterContainingException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
    }

    protected function verifyPassword(string $oldPassword, Password $userPassword): void
    {
        if (false === Password::verify($oldPassword, $userPassword)) {
            throw new IncorrectPasswordException();
        }
    }

    protected function createEmailFromPrimitive(string $email): Email
    {
        try {
            return new Email($email);
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
    }

    protected function ensureNewEmailIsAvailable(string $newEmail, string $oldEmail): void
    {
        $userWithNewEmail = $this->userRepository->ofEmail($this->createEmailFromPrimitive($newEmail));
        if (null !== $userWithNewEmail && $newEmail !== $oldEmail) {
            throw new UserAlreadyExistsException();
        }
    }

    protected function createRolesFromPrimitive(array $roles): Roles
    {
        try {
            return Roles::fromArrayOfPrimitives($roles);
        } catch (InvalidRoleException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
    }

    protected function ensureUserDoesntExistByEmail(Email $email): void
    {
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
        if (false === $user->getEnabled()) {
            throw new UserNotEnabledException();
        }
    }

    protected function validateConfirmationTokenFromRequest(User $user, Token $tokenFromRequest): void
    {
        if (null === $user->getConfirmationToken()) {
            throw new ConfirmationTokenNotFoundException();
        }
        if (false === $user->confirmationTokenEqualsTo($tokenFromRequest)) {
            throw new IncorrectConfirmationTokenException();
        }
    }

    protected function ensureRequestAuthorIsUser(User $requestAuthor, User $user): void
    {
        if (false === $requestAuthor->idEqualsTo($user->getId())) {
            throw new PermissionDeniedException();
        }
    }

    protected function ensureRolesAreValid(Roles $roles): void {
        if ($roles->contains(new Role(Role::ADMIN))) {
            throw new PermissionDeniedException();
        }
    }

    protected function createNameFromPrimitive(string $name): Name {
        try {
            return new Name($name);
        } catch (InvalidNameException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
    }

}