<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User\Create;

use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Roles;
use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class CreateUser
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateUserRequest $request): void
    {
        $user = $this->repository->searchByEmail($request->getEmail());

        if (null !== $user) {
            throw new UserAlreadyExistsException();
        }

        $roles = Roles::fromPrimitives($request->getRoles());

        $user = new User();
        $user->setUuid($request->getUuid());
        $user->setEmail($request->getEmail());
        $user->setPassword($request->getPassword());
        $user->setFirstName($request->getFirstName());
        $user->setLastName($request->getLastName());
        $user->setRoles($roles);
        $user->setCreated(new \DateTimeImmutable());

        $this->repository->save($user);

    }

}