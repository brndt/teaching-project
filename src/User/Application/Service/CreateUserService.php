<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class CreateUserService
{
    public function __construct(
        private RandomStringGenerator $randomStringGenerator,
        UserRepository $userRepository,
        private DomainEventBus $eventBus,
        AuthorizationService $authorizationService
    ) {
        $this->repository = $userRepository;
        $this->userService = new UserService($userRepository);
        $this->authorizationService = $authorizationService;
    }

    public function __invoke(CreateUserRequest $request): void
    {
        $email = new Email($request->getEmail());
        $this->userService->ensureUserDoesntExistByEmail($email);

        $password = Password::fromPlainPassword($request->getPassword());

        $firstName = new Name($request->getFirstName());
        $lastName = new Name($request->getLastName());

        $roles = Roles::fromArrayOfPrimitives($request->getRoles());
        $roles->ensureRolesDontContainsAdmin();

        $user = User::create(
            $this->repository->nextIdentity(),
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

        $this->repository->save($user);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }
}
