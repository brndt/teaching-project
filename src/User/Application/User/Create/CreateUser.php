<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User\Create;

use LaSalle\StudentTeacher\Shared\Domain\DomainEventBus;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Domain\PasswordHashing;
use LaSalle\StudentTeacher\User\Domain\Roles;
use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class CreateUser
{
    private UserRepository $repository;
    private PasswordHashing $passwordHashing;
    private DomainEventBus $eventBus;

    public function __construct(UserRepository $repository, PasswordHashing $passwordHashing, DomainEventBus $eventBus)
    {
        $this->repository = $repository;
        $this->passwordHashing = $passwordHashing;
        $this->eventBus = $eventBus;
    }

    public function __invoke(CreateUserRequest $request): void
    {
        $user = $this->repository->searchByEmail($request->getEmail());

        if (null !== $user) {
            throw new UserAlreadyExistsException();
        }

        $user = User::create(
            $request->getUuid(),
            $request->getEmail(),
            $this->passwordHashing->hash_password($request->getPassword()),
            $request->getFirstName(),
            $request->getLastName(),
            Roles::fromPrimitives($request->getRoles()),
            new \DateTimeImmutable()
        );

        $this->repository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }

}