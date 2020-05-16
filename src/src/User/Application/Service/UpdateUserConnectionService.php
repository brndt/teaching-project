<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\UpdateUserConnectionRequest;

final class UpdateUserConnectionService extends UserConnectionService
{
    public function __invoke(UpdateUserConnectionRequest $request)
    {
        $authorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($authorId);
        $this->ensureUserExists($requestAuthor);

        $firstUserId = $this->createIdFromPrimitive($request->getFirstUser());
        $firstUser = $this->userRepository->ofId($firstUserId);
        $this->ensureUserExists($firstUser);

        $secondUserId = $this->createIdFromPrimitive($request->getSecondUser());
        $secondUser = $this->userRepository->ofId($secondUserId);
        $this->ensureUserExists($secondUser);

        $newSpecifier = $this->identifySpecifier($authorId, $firstUser, $secondUser);

        [$student, $teacher] = $this->identifyStudentAndTeacher($firstUser, $secondUser);

        $userConnection = $this->userConnectionRepository->ofId($student->getId(), $teacher->getId());
        $this->ensureConnectionExists($userConnection);

        $newState = $this->stateFactory->create($request->getStatus());
        $isSpecifierChanged = $this->verifySpecifierChanged($newSpecifier->getId(), $userConnection->getSpecifierId());

        $userConnection->setState($newState, $isSpecifierChanged);
        $userConnection->setSpecifierId($newSpecifier->getId());

        $this->userConnectionRepository->save($userConnection);
    }
}