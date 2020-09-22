<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateVideoResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\VideoResourceStudentAnswer;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceStudentAnswerRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\ResourceService;
use LaSalle\StudentTeacher\Resource\Domain\Service\ResourceStudentAnswerService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\StudentTestAnswer;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class CreateVideoResourceStudentAnswerService
{
    private ResourceStudentAnswerRepository $repository;
    private UserService $userService;
    private ResourceService $resourceService;
    private ResourceStudentAnswerService $resourceStudentAnswerService;
    private AuthorizationService $authorizationService;

    public function __construct(
        UserRepository $userRepository,
        AuthorizationService $authorizationService,
        ResourceRepository $resourceRepository,
        ResourceStudentAnswerRepository $repository
    ) {
        $this->userService = new UserService($userRepository);
        $this->resourceService = new ResourceService($resourceRepository);
        $this->resourceStudentAnswerService = new ResourceStudentAnswerService($repository);
        $this->authorizationService = $authorizationService;
        $this->repository = $repository;
    }

    public function __invoke(CreateVideoResourceStudentAnswerRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $resourceId = new Uuid($request->getResourceId());
        $resource = $this->resourceService->findResource($resourceId);

        $id = $this->repository->nextIdentity();

        $resourceId = new Uuid($request->getResourceId());

        $this->resourceStudentAnswerService->ensureStudentAnswerNotExists($requestAuthorId, $resourceId);

        $testResourceStudentAnswer = new VideoResourceStudentAnswer(
            $id,
            $resourceId,
            $requestAuthorId,
            null,
            null,
            new \DateTimeImmutable(),
            null,
            null,
            new Status(Status::PUBLISHED),
            $request->getStudentAnswer()
        );

        $this->authorizationService->ensureStudentHasAccessToResource($requestAuthorId, $resource);

        $this->repository->save($testResourceStudentAnswer);
    }
}
