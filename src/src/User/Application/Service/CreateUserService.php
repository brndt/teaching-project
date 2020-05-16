<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class CreateUserService extends UserService
{
    private DomainEventBus $eventBus;

    public function __construct(UserRepository $userRepository, DomainEventBus $eventBus)
    {
        parent::__construct($userRepository);
        $this->eventBus = $eventBus;
    }

    public function __invoke(CreateUserRequest $request): void
    {
        $email = $this->createEmailFromPrimitive($request->getEmail());
        $this->ensureUserDoesntExistByEmail($email);

        $firstName = $this->createNameFromPrimitive($request->getFirstName());
        $lastName = $this->createNameFromPrimitive($request->getLastName());

        $roles = $this->createRolesFromPrimitive($request->getRoles());
        $this->ensureRolesDontContainsAdmin($roles);

        $user = User::create(
            $this->userRepository->nextIdentity(),
            $email,
            $this->createPasswordFromPrimitive($request->getPassword()),
            $firstName,
            $lastName,
            $this->createRolesFromPrimitive($request->getRoles()),
            $request->getCreated(),
            false
        );

        $this->userRepository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }
}