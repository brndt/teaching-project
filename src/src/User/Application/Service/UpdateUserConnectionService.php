<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserConnectionService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\StateFactory;

final class UpdateUserConnectionService
{
    private UserRepository $userRepository;
    private UserConnectionRepository $userConnectionRepository;
    private UserService $userService;
    private AuthorizationService $authorizationService;
    private UserConnectionService $userConnectionService;
    private StateFactory $stateFactory;

    public function __construct(
        UserConnectionRepository $userConnectionRepository,
        UserRepository $userRepository,
        StateFactory $stateFactory
    ) {
        $this->userRepository = $userRepository;
        $this->userConnectionRepository = $userConnectionRepository;
        $this->stateFactory = $stateFactory;
        $this->userService = new UserService($this->userRepository);
        $this->authorizationService = new AuthorizationService();
        $this->userConnectionService = new UserConnectionService($this->userConnectionRepository);
    }

    public function __invoke(UpdateUserConnectionRequest $request)
    {
        $authorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($authorId);

        $firstUserId = new Uuid($request->getFirstUser());
        $firstUser = $this->userService->findUser($firstUserId);

        $secondUserId = new Uuid($request->getSecondUser());
        $secondUser = $this->userService->findUser($secondUserId);

        $newSpecifier = $this->userConnectionService->identifySpecifier($requestAuthor, $firstUser, $secondUser);

        [$student, $teacher] = $this->userConnectionService->identifyStudentAndTeacher($firstUser, $secondUser);

        $userConnection = $this->userConnectionService->findUserConnection($student, $teacher);

        $newState = $this->stateFactory->create($request->getStatus());
        $isSpecifierChanged = $this->userConnectionService->verifySpecifierChanged($newSpecifier->getId(), $userConnection->getSpecifierId());

        $userConnection->setState($newState, $isSpecifierChanged);

        $userConnection->setSpecifierId($newSpecifier->getId());

        $this->userConnectionRepository->save($userConnection);
    }
}
