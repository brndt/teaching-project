<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;

final class UpdateUserPasswordService extends UserService
{
    public function __invoke(UpdateUserPasswordRequest $request): void
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $userId = $this->createIdFromPrimitive($request->getUserId());

        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $userToUpdate = $this->userRepository->ofId($userId);
        $this->ensureUserExists($userToUpdate);

        $this->ensureRequestAuthorHasPermissions($requestAuthor, $userToUpdate);

        $this->verifyPassword($request->getOldPassword(), $userToUpdate->getPassword());

        $userToUpdate->setPassword($this->createPasswordFromPrimitive($request->getNewPassword()));

        $this->userRepository->save($userToUpdate);
    }
}