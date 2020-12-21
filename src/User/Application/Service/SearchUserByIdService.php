<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserByIdRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class SearchUserByIdService
{
    private UserRepository $repository;
    private UserService $userService;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(SearchUserByIdRequest $request): UserResponse
    {
        $userId = new Uuid($request->getUserId());
        $user = $this->userService->findUser($userId);
        return $this->buildUserResponse($user);
    }

    private function buildUserResponse(User $user): UserResponse
    {
        return new UserResponse(
            $user->getId()->toString(),
            $user->getFirstName()->toString(),
            $user->getLastName()->toString(),
            $user->getRoles()->getArrayOfPrimitives(),
            $user->getCreated(),
            $user->getImage(),
            $user->getExperience(),
            $user->getEducation(),
        );
    }
}
