<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;

final class UpdateUserInformationService extends UserService
{
    public function __invoke(UpdateUserInformationRequest $request): void
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $userId = $this->createIdFromPrimitive($request->getUserId());
        $userToUpdate = $this->userRepository->ofId($userId);
        $this->ensureUserExists($userToUpdate);

        $this->ensureRequestAuthorIsUser($requestAuthor, $userToUpdate);

        $email = $this->createEmailFromPrimitive($request->getEmail());
        $this->ensureNewEmailIsAvailable($email, $userToUpdate->getEmail());

        $firstName = $this->createNameFromPrimitive($request->getFirstName());
        $lastName = $this->createNameFromPrimitive($request->getLastName());

        $userToUpdate->setEmail($email);
        $userToUpdate->setFirstName($firstName);
        $userToUpdate->setLastName($lastName);
        $userToUpdate->setEducation($request->getEducation());
        $userToUpdate->setExperience($request->getExperience());

        $this->userRepository->save($userToUpdate);
    }
}
