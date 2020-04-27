<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
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
        try {
            $email = new Email($request->getEmail());
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        $user = $this->userRepository->ofEmail($email);

        if (null !== $user) {
            throw new UserAlreadyExistsException();
        }

        try {
            $password = Password::fromPlainPassword($request->getPassword());
        } catch (InvalidPasswordLengthException | InvalidNumberContainingException | InvalidLetterContainingException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        try {
            $roles = Roles::fromArrayOfPrimitives($request->getRoles());
        } catch (InvalidRoleException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        $userId = $this->userRepository->nextIdentity();

        $user = User::create(
            $userId,
            $email,
            $password,
            $request->getFirstName(),
            $request->getLastName(),
            $roles,
            $request->getCreated()
        );

        $this->userRepository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }

}