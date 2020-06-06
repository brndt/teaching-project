<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\SearchUserConnectionByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

final class SearchUserConnectionByIdService extends UserConnectionService
{
    public function __invoke(SearchUserConnectionByCriteriaRequest $request)
    {
        $authorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $author = $this->userRepository->ofId($authorId);
        $this->ensureUserExists($author);

        $userId = $this->createIdFromPrimitive($request->getUserId());
        $user = $this->userRepository->ofId($userId);
        $this->ensureUserExists($user);

        $friendId = $this->createIdFromPrimitive($request->getFriendId());
        $friend = $this->userRepository->ofId($friendId);
        $this->ensureUserExists($friend);

        $this->ensureRequestAuthorHasPermissions($author, $user);

        [$student, $teacher] = $this->identifyStudentAndTeacher($user, $friend);

        $connection = $this->userConnectionRepository->ofId($student->getId(), $teacher->getId());

        if (true === $user->isInRole(new Role(Role::STUDENT))) {
            return $this->buildStudentResponse($connection);
        }

        return $this->buildTeacherResponse($connection);
    }

    private function buildStudentResponse(UserConnection $connection): UserConnectionResponse
    {
        return new UserConnectionResponse(
            $connection->getStudentId()->toString(),
            $connection->getTeacherId()->toString(),
            (string)$connection->getState(),
            $connection->getSpecifierId()->toString()
        );
    }

    private function buildTeacherResponse(UserConnection $connection): UserConnectionResponse
    {
        return new UserConnectionResponse(
            $connection->getStudentId()->toString(),
            $connection->getTeacherId()->toString(),
            (string)$connection->getState(),
            $connection->getSpecifierId()->toString()
        );
    }
}
