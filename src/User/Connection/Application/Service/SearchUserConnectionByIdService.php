<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Connection\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Connection\Application\Request\SearchUserConnectionByCriteriaRequest;
use LaSalle\StudentTeacher\User\Connection\Application\Response\UserConnectionResponse;
use LaSalle\StudentTeacher\User\Connection\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Connection\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Connection\Domain\Service\UserConnectionService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Role;

final class SearchUserConnectionByIdService
{
    private UserService $userService;
    private UserConnectionService $userConnectionService;

    public function __construct(
        private UserConnectionRepository $userConnectionRepository,
        private UserRepository $userRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($this->userRepository);
        $this->userConnectionService = new UserConnectionService($this->userConnectionRepository);
    }

    public function __invoke(SearchUserConnectionByCriteriaRequest $request)
    {
        $authorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($authorId);

        $userId = new Uuid($request->getUserId());
        $user = $this->userService->findUser($userId);

        $friendId = new Uuid($request->getFriendId());
        $friend = $this->userService->findUser($friendId);

        $this->authorizationService->ensureRequestAuthorHasPermissionsToUserConnection($requestAuthor, $user);

        [$student, $teacher] = $this->userConnectionService->identifyStudentAndTeacher($user, $friend);

        $connection = $this->userConnectionService->findUserConnection($student, $teacher);

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
