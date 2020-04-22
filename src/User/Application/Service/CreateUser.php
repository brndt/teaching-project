<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
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
        $user = $this->repository->searchByEmail(new Email($request->getEmail()));

        if (null !== $user) {
            throw new UserAlreadyExistsException();
        }

        $user = User::create(
            Uuid::generate(),
            new Email($request->getEmail()),
            Password::fromPlainPassword($request->getPassword()),
            $request->getFirstName(),
            $request->getLastName(),
            Roles::fromArrayOfPrimitives($request->getRoles()),
            new \DateTimeImmutable()
        );

        $this->repository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }

}