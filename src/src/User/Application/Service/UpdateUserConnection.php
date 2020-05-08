<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFound;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserConnectionRequest;

final class UpdateUserConnection extends UserConnectionService
{
    public function __invoke(UpdateUserConnectionRequest $request)
    {
        $author = $this->recognizeSpecifier(
            $request->getRequestAuthorId(),
            $request->getUserId(),
            $request->getFriendId()
        );

        $firstUser = $this->identifyUserById($request->getUserId());
        $secondUser = $this->identifyUserById($request->getFriendId());

        [$student, $teacher] = $this->verifyStudentAndTeacher($firstUser, $secondUser);

        $userConnection = $this->userConnectionRepository->ofId($student->getId(), $teacher->getId());

        if (null === $userConnection) {
            throw new ConnectionNotFound();
        }

        $newState = $this->stateFactory->create($request->getStatus());
        $isSpecifierChanged = $this->verifySpecifierChanged($author, $userConnection->getSpecifierId());

        $userConnection->setState($newState, $isSpecifierChanged);
        $userConnection->setSpecifierId($author);

        $this->userConnectionRepository->save($userConnection);
    }
}