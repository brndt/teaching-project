<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;

final class UpdateUserInformationService extends UserService
{
    public function __invoke(UpdateUserInformationRequest $request): void
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $userId = $this->createIdFromPrimitive($request->getUserId());

        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $userToUpdate = $this->userRepository->ofId($userId);
        $this->ensureUserExists($userToUpdate);

        $this->ensureRequestAuthorHasPermissions($requestAuthor, $userToUpdate);

        $this->ensureNewEmailIsAvailable($request->getEmail(), $userToUpdate->getEmail()->toString());

        $userToUpdate->setEmail($this->createEmailFromPrimitive($request->getEmail()));
        $userToUpdate->setFirstName($request->getFirstName());
        $userToUpdate->setLastName($request->getLastName());
        $userToUpdate->setImage($request->getImage());
        $userToUpdate->setEducation($request->getEducation());
        $userToUpdate->setExperience($request->getExperience());

        $this->userRepository->save($userToUpdate);
    }
}