<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;

final class UpdateUserPasswordService
{
    private UserService $userService;

    public function __construct(private UserRepository $repository, private AuthorizationService $authorizationService)
    {
        $this->userService = new UserService($repository);
    }

    public function __invoke(UpdateUserPasswordRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $userId = new Uuid($request->getUserId());

        $requestAuthor = $this->userService->findUser($requestAuthorId);
        $userToUpdate = $this->userService->findUser($userId);

        $this->authorizationService->ensureRequestAuthorIsCertainUser($requestAuthor, $userToUpdate);
        $userToUpdate->getPassword()->verify($request->getOldPassword());

        $userToUpdate->setPassword(Password::fromPlainPassword($request->getNewPassword()));

        $this->repository->save($userToUpdate);
    }
}