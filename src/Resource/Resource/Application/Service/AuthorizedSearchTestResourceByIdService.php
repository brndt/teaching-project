<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Course\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Resource\Application\Request\AuthorizedSearchTestResourceByIdRequest;
use LaSalle\StudentTeacher\Resource\Resource\Application\Response\TestResourceResponse;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Aggregate\Resource;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Service\ResourceService;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\ValueObject\TestQuestion;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;

final class AuthorizedSearchTestResourceByIdService
{
    private CourseService $courseService;
    private UserService $userService;
    private ResourceService $resourceService;
    private UnitService $unitService;

    public function __construct(
        CourseRepository $courseRepository,
        UserRepository $userRepository,
        UnitRepository $unitRepository,
        ResourceRepository $resourceRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->resourceService = new ResourceService($resourceRepository);
        $this->unitService = new UnitService($unitRepository);
        $this->courseService = new CourseService($courseRepository);
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(AuthorizedSearchTestResourceByIdRequest $request): TestResourceResponse
    {
        $authorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($authorId);

        $resourceId = new Uuid($request->getResourceId());
        $resource = $this->resourceService->findResource($resourceId);

        $unit = $this->unitService->findUnit($resource->getUnitId());
        $course = $this->courseService->findCourse($unit->getCourseId());

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        return $this->buildResponse($resource);
    }

    private function buildResponse(Resource $resource): TestResourceResponse
    {
        return new TestResourceResponse(
            $resource->getId()->toString(),
            $resource->getUnitId()->toString(),
            $resource->getName(),
            $resource->getDescription(),
            $resource->getCreated(),
            $resource->getModified(),
            $resource->getStatus()->value(),
            $resource->getContent(),
            array_map($this->testQuestionTransformer(), $resource->getQuestions()),
        );
    }

    private function testQuestionTransformer(): callable
    {
        return static function (TestQuestion $testQuestion): array {
            return $testQuestion->toValues();
        };
    }

}
