<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\UpdateUserImageRequest;

final class UpdateUserImageService extends UserService
{
    public function __invoke(UpdateUserImageRequest $request): void
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $userId = $this->createIdFromPrimitive($request->getUserId());
        $userToUpdate = $this->userRepository->ofId($userId);
        $this->ensureUserExists($userToUpdate);

        $this->ensureRequestAuthorIsUser($requestAuthor, $userToUpdate);

        $userToUpdate->setImage($request->getImage());

        $this->userRepository->save($userToUpdate);
    }
}
