<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User\Search;

use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\User\UserResponse;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class SearchUserByEmail
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SearchUserByEmailRequest $request): UserResponse
    {
        $user = $this->repository->searchByEmail($request->getEmail());

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return new UserResponse(
            $user->getId()->getValue(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getRoles()->toPrimitives(),
            $user->getCreated()->format('Y-m-d H:i:s'),
            $user->getImage(),
            $user->getExperience(),
            $user->getEducation(),
        );
    }
}