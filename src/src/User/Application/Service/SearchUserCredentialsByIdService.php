<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserCredentialsByIdRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserCredentialsResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class SearchUserCredentialsByIdService
{
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(SearchUserCredentialsByIdRequest $request): UserCredentialsResponse
    {
        $userId = new Uuid($request->getUserId());
        $user = $this->userService->findUser($userId);
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
