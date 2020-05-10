<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\UpdateUserConnectionRequest;

final class UpdateUserConnectionService extends UserConnectionService
{
    public function __invoke(UpdateUserConnectionRequest $request)
    {
        $authorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $userId = $this->createIdFromPrimitive($request->getUserId());
        $friendId = $this->createIdFromPrimitive($request->getFriendId());

        $firstUser = $this->searchUserById($userId);
        $secondUser = $this->searchUserById($friendId);

        $specifier = $this->recognizeSpecifier($authorId, $firstUser, $secondUser);

        $this->ensureRequestAuthorIsTeacherOrStudent($specifier, $firstUser, $secondUser);

        [$student, $teacher] = $this->verifyStudentAndTeacher($firstUser, $secondUser);

        $userConnection = $this->userConnectionRepository->ofId($student->getId(), $teacher->getId());

        $this->ensureConnectionExists($userConnection);

        $newState = $this->stateFactory->create($request->getStatus());
        $isSpecifierChanged = $this->verifySpecifierChanged($specifier, $userConnection);

        $userConnection->setState($newState, $isSpecifierChanged);
        $userConnection->setSpecifierId($specifier->getId());

        $this->userConnectionRepository->save($userConnection);
    }
}