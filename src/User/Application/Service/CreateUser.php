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
    private UserRepository $repository;
    private DomainEventBus $eventBus;

    public function __construct(UserRepository $repository, DomainEventBus $eventBus)
    {
        $this->repository = $repository;
        $this->eventBus = $eventBus;
    }

    public function __invoke(CreateUserRequest $request): void
    {
        try {
            $email = new Email($request->getEmail());
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        $user = $this->repository->searchByEmail($email);

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

        $user = User::create(
            Uuid::generate(),
            $email,
            $password,
            $request->getFirstName(),
            $request->getLastName(),
            $roles,
            new DateTimeImmutable()
        );

        $this->repository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }

}