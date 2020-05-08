<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\CreateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\Pended;

final class CreateUserConnection extends UserConnectionService
{
    public function __invoke(CreateUserConnectionRequest $request): void
    {
        $authorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $firstUser = $this->identifyUserById($request->getUserId());
        $secondUser = $this->identifyUserById($request->getFriendId());

        [$student, $teacher] = $this->verifyStudentAndTeacher($firstUser, $secondUser);
        $this->ensureConnectionDoesntExists($student, $teacher);

        $userConnection = new UserConnection($student->getId(), $teacher->getId(), new Pended(), $authorId);

        $this->userConnectionRepository->save($userConnection);
    }
}