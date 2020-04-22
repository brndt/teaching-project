<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserByIdRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class SearchUserById
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SearchUserByIdRequest $request): UserResponse
    {
        $user = $this->repository->searchById(Uuid::fromString($request->getId()));

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return new UserResponse(
            $user->getId()->toString(),
            $user->getEmail()->toString(),
            $user->getPassword()->toString(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getRoles()->toArrayOfPrimitives(),
            $user->getCreated()->format('Y-m-d H:i:s'),
            $user->getImage(),
            $user->getExperience(),
            $user->getEducation(),
        );
    }
}