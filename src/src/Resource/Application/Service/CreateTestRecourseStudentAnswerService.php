<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateTestRecourseStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\TestRecourseStudentAnswer;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\RecourseStudentAnswerRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\Service\ResourceService;
use LaSalle\StudentTeacher\Resource\Domain\Service\ResourceStudentAnswerService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\StudentTestAnswer;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class CreateTestRecourseStudentAnswerService
{
    private RecourseStudentAnswerRepository $repository;
    private UserService $userService;
    private ResourceService $resourceService;
    private ResourceStudentAnswerService $resourceStudentAnswerService;
    private AuthorizationService $authorizationService;

    public function __construct(UserRepository $userRepository, AuthorizationService $authorizationService, ResourceRepository $resourceRepository, RecourseStudentAnswerRepository $repository)
    {
        $this->userService = new UserService($userRepository);
        $this->resourceService = new ResourceService($resourceRepository);
        $this->resourceStudentAnswerService = new ResourceStudentAnswerService($repository);
        $this->authorizationService = $authorizationService;
        $this->repository = $repository;
    }

    public function __invoke(CreateTestRecourseStudentAnswerRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $recourseId = new Uuid($request->getRecourseId());
        $recourse = $this->resourceService->findResource($recourseId);

        $id = $this->repository->nextIdentity();

        $recourseId = new Uuid($request->getRecourseId());

        $this->resourceStudentAnswerService->ensureStudentAnswerNotExists($requestAuthorId, $recourseId);

        $testRecourseStudentAnswer = new TestRecourseStudentAnswer(
            $id,
            $recourseId,
            $requestAuthorId,
            null,
            null,
            new \DateTimeImmutable(),
            null,
            null,
            new Status(Status::PUBLISHED),
            ...array_map($this->assumptionMaker(), $request->getAssumptions()),
        );

        $this->authorizationService->ensureStudentHasAccessToRecourse($requestAuthorId, $recourse);
        
        $this->repository->save($testRecourseStudentAnswer);
    }

    private function assumptionMaker(): callable
    {
        return static function (array $values): StudentTestAnswer {
            return StudentTestAnswer::fromValues($values);
        };
    }
}
