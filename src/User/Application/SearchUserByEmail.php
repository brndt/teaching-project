<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application;

use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class SearchUserByEmail
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SearchUserByEmailRequest $request): ?UserResponse
    {
        $user = $this->repository->searchByEmail($request->getEmail());

        if (null === $user) {
            return null;
        }
        return new UserResponse($user->getEmail(), $user->getPassword(), $user->getFirstName(), $user->getLastName(), $user->getRoles()->toPrimitives(), $user->getId(), $user->getImage(), $user->getEducation(), $user->getExperience(), $user->getCreated());
    }
}