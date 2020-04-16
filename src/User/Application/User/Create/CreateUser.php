<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User\Create;

use LaSalle\StudentTeacher\Shared\Domain\DomainEventBus;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Domain\Roles;
use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

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
        $user = $this->repository->searchByEmail($request->getEmail());

        if (null !== $user) {
            throw new UserAlreadyExistsException();
        }

        $roles = Roles::fromPrimitives($request->getRoles());

        $user = User::create(
            $request->getUuid(),
            $request->getEmail(),
            $request->getPassword(),
            $request->getFirstName(),
            $request->getLastName(),
            $roles,
            new \DateTimeImmutable()
        );

        $this->repository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }

}