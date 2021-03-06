<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Service\ResourceService;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Application\Request\CreateVideoResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\Aggregate\VideoResourceStudentAnswer;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\Repository\ResourceStudentAnswerRepository;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\Service\ResourceStudentAnswerService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;

final class CreateVideoResourceStudentAnswerService
{
    private UserService $userService;
    private ResourceService $resourceService;
    private ResourceStudentAnswerService $resourceStudentAnswerService;

    public function __construct(
        UserRepository $userRepository,
        private AuthorizationService $authorizationService,
        ResourceRepository $resourceRepository,
        private ResourceStudentAnswerRepository $repository
    ) {
        $this->userService = new UserService($userRepository);
        $this->resourceService = new ResourceService($resourceRepository);
        $this->resourceStudentAnswerService = new ResourceStudentAnswerService($repository);
    }

    public function __invoke(CreateVideoResourceStudentAnswerRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $resourceId = new Uuid($request->getResourceId());
        $resource = $this->resourceService->findResource($resourceId);

        $this->resourceStudentAnswerService->ensureStudentAnswerNotExists($requestAuthorId, $resourceId);

        $this->authorizationService->ensureUserHasAccessToResource($requestAuthor, $resource);

        $testResourceStudentAnswer = new VideoResourceStudentAnswer(
            $this->repository->nextIdentity(),
            $resourceId,
            $requestAuthorId,
            null,
            null,
            new DateTimeImmutable(),
            null,
            null,
            new Status(Status::PUBLISHED),
            $request->getStudentAnswer()
        );

        $this->repository->save($testResourceStudentAnswer);
    }
}
