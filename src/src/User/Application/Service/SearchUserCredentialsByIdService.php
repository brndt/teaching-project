<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\SearchUserCredentialsByIdRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserCredentialsResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

final class SearchUserCredentialsByIdService extends UserService
{
    public function __invoke(SearchUserCredentialsByIdRequest $request): UserCredentialsResponse
    {
        $userId = $this->createIdFromPrimitive($request->getUserId());

        $user = $this->userRepository->ofId($userId);
        $this->ensureUserExists($user);

        return $this->buildUserCredentialsResponse($user);
    }

    private function buildUserCredentialsResponse(User $user): UserCredentialsResponse
    {
        return new UserCredentialsResponse(
            $user->getId()->toString(),
            $user->getEmail()->toString(),
            $user->getPassword()->toString(),
            $user->getRoles()->getArrayOfPrimitives(),
            $user->getEnabled()
        );
    }
}