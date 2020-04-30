<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
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

final class CreateUser
{
    private UserRepository $userRepository;
    private DomainEventBus $eventBus;

    public function __construct(UserRepository $userRepository, DomainEventBus $eventBus)
    {
        $this->userRepository = $userRepository;
        $this->eventBus = $eventBus;
    }

    public function __invoke(CreateUserRequest $request): void
    {
        $this->checkIfUserExistsByEmail($request->getEmail());

        $user = User::create(
            $this->userRepository->nextIdentity(),
            $this->createEmailFromPrimitive($request->getEmail()),
            $this->createPasswordFromPrimitive($request->getPassword()),
            $request->getFirstName(),
            $request->getLastName(),
            $this->createRolesFromPrimitive($request->getRoles()),
            $request->getCreated(),
            false
        );

        $this->userRepository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }

    private function checkIfUserExistsByEmail(string $email): void
    {
        $email = $this->createEmailFromPrimitive($email);
        if (null !== $this->userRepository->ofEmail($email)) {
            throw new UserAlreadyExistsException();
        }
    }

    private function createEmailFromPrimitive(string $email): Email
    {
        try {
            return new Email($email);
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
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

    private function createRolesFromPrimitive(array $roles): Roles
    {
        try {
            return Roles::fromArrayOfPrimitives($roles);
        } catch (InvalidRoleException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }
    }
}