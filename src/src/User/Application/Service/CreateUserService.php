<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class CreateUserService extends UserService
{
    private DomainEventBus $eventBus;
    private RandomStringGenerator $randomStringGenerator;

    public function __construct(RandomStringGenerator $randomStringGenerator, UserRepository $userRepository, DomainEventBus $eventBus)
    {
        parent::__construct($userRepository);
        $this->eventBus = $eventBus;
        $this->randomStringGenerator = $randomStringGenerator;
    }

    public function __invoke(CreateUserRequest $request): void
    {
        $email = $this->createEmailFromPrimitive($request->getEmail());
        $this->ensureUserDoesntExistByEmail($email);

        $password = $this->createPasswordFromPrimitive($request->getPassword());

        $firstName = $this->createNameFromPrimitive($request->getFirstName());
        $lastName = $this->createNameFromPrimitive($request->getLastName());

        $roles = $this->createRolesFromPrimitive($request->getRoles());
        $this->ensureRolesDontContainsAdmin($roles);

        $user = User::create(
            $this->userRepository->nextIdentity(),
            $email,
            $password,
            $firstName,
            $lastName,
            $roles,
            $request->getCreated(),
            false,
            null,
            null,
            null,
            new Token($this->randomStringGenerator->generate()),
            new DateTimeImmutable('+1 day')
        );

        $this->userRepository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }
}
