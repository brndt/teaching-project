<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchTestResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\TestResourceStudentAnswerResponse;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceStudentAnswerRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CoursePermissionService;
use LaSalle\StudentTeacher\Resource\Domain\Service\ResourceService;
use LaSalle\StudentTeacher\Resource\Domain\Service\ResourceStudentAnswerService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\StudentTestAnswer;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class AuthorizedSearchTestResourceStudentAnswerService
{
    private ResourceService $resourceService;
    private UserService $userService;
    private AuthorizationService $authorizationService;
    private CoursePermissionService $coursePermissionService;
    private ResourceStudentAnswerService $resourceStudentAnswerService;

    public function __construct(
        ResourceRepository $resourceRepository,
        UserRepository $userRepository,
        CoursePermissionRepository $coursePermissionRepository,
        AuthorizationService $authorizationService,
        ResourceStudentAnswerRepository $resourceStudentAnswerRepository
    ) {
        $this->userService = new UserService($userRepository);
        $this->resourceService = new ResourceService($resourceRepository);
        $this->authorizationService = $authorizationService;
        $this->coursePermissionService = new CoursePermissionService($coursePermissionRepository);
        $this->resourceStudentAnswerService = new ResourceStudentAnswerService($resourceStudentAnswerRepository);
    }

    public function __invoke(AuthorizedSearchTestResourceStudentAnswerRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $resourceId = new Uuid($request->getResourceId());
        $resource = $this->resourceService->findResource($resourceId);

        $studentId = new Uuid($request->getStudentId());
        $this->userService->findUser($studentId);

        $resourceStudentAnswer = $this->resourceStudentAnswerService->findResourceStudentAnswer(
            $resourceId,
            $studentId
        );

        $this->authorizationService->ensureUserHasAccessToResource($requestAuthor, $resource);

        return new TestResourceStudentAnswerResponse(
            $resourceStudentAnswer->getId()->toString(),
            $resourceStudentAnswer->getResourceId()->toString(),
            $resourceStudentAnswer->getStudentId()->toString(),
            $resourceStudentAnswer->getPoints(),
            $resourceStudentAnswer->getTeacherComment(),
            $resourceStudentAnswer->getCreated(),
            $resourceStudentAnswer->getModified(),
            $resourceStudentAnswer->getUntil(),
            $resourceStudentAnswer->getStatus()->value(),
            array_map($this->testAssumptionsTransformer(), $resourceStudentAnswer->getAssumptions()),
        );
    }

    private function testAssumptionsTransformer(): callable
    {
        return static function (StudentTestAnswer $testAssumption): array {
            return $testAssumption->toValues();
        };
    }
}
