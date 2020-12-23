<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateVideoResourceRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\VideoResource;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\Service\ResourceService;
use LaSalle\StudentTeacher\Resource\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class CreateVideoResourceService
{
    private UserService $userService;
    private UnitService $unitService;
    private ResourceService $resourceService;
    private CourseService $courseService;

    public function __construct(
        CourseRepository $courseRepository,
        private UnitRepository $unitRepository,
        UserRepository $userRepository,
        private ResourceRepository $resourceRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($userRepository);
        $this->courseService = new CourseService($courseRepository);
        $this->unitService = new UnitService($unitRepository);
        $this->resourceService = new ResourceService($resourceRepository);
    }

    public function __invoke(CreateVideoResourceRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthor());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $unitId = new Uuid($request->getUnitId());
        $unit = $this->unitService->findUnit($unitId);

        $course = $this->courseService->findCourse($unit->getCourseId());

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        $resource = new VideoResource(
            $this->resourceRepository->nextIdentity(),
            $unitId,
            $request->getName(),
            $request->getDescription(),
            $request->getContent(),
            new \DateTimeImmutable(),
            null,
            new Status($request->getStatus()),
            $request->getVideoUrl(),
            $request->getVideoDescription()
        );

        $this->resourceRepository->save($resource);
    }
}
