<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserImageRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class UpdateUserImageService
{
    private UserService $userService;

    public function __construct(private UserRepository $repository, private AuthorizationService $authorizationService)
    {
        $this->userService = new UserService($repository);
    }

    public function __invoke(UpdateUserImageRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $userId = new Uuid($request->getUserId());

        $requestAuthor = $this->userService->findUser($requestAuthorId);
        $userToUpdate = $this->userService->findUser($userId);

        $this->authorizationService->ensureRequestAuthorIsCertainUser($requestAuthor, $userToUpdate);

        $userToUpdate->setImage($request->getImage());

        $this->repository->save($userToUpdate);
    }
}
