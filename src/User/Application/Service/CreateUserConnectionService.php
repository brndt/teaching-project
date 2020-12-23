<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserConnectionService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\Pended;

final class CreateUserConnectionService
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

    public function __invoke(CreateUserConnectionRequest $request): void
    {
        $authorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($authorId);

        $firstUserId = new Uuid($request->getFirstUser());
        $firstUser = $this->userService->findUser($firstUserId);

        $secondUserId = new Uuid($request->getSecondUser());
        $secondUser = $this->userService->findUser($secondUserId);

        $this->authorizationService->ensureRequestAuthorIsOneOfUsers($requestAuthor, $firstUser, $secondUser);

        [$student, $teacher] = $this->userConnectionService->identifyStudentAndTeacher($firstUser, $secondUser);
        $this->userConnectionService->ensureConnectionDoesntExists($student, $teacher);

        $userConnection = new UserConnection($student->getId(), $teacher->getId(), new Pended(), $authorId);

        $this->userConnectionRepository->save($userConnection);
    }
}
