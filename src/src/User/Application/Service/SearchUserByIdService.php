<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\SearchUserByIdRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

final class SearchUserByIdService extends UserService
{
    public function __invoke(SearchUserByIdRequest $request): UserResponse
    {
        $userId = $this->createIdFromPrimitive($request->getUserId());
        $user = $this->userRepository->ofId($userId);
        $this->ensureUserExists($user);

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
