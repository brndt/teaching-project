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
    private UnitRepository $unitRepository;
    private AuthorizationService $authorizationService;
    private UnitService $unitService;
    private ResourceService $resourceService;
    private ResourceRepository $resourceRepository;
    private CourseService $courseService;

    public function __construct(
        CourseRepository $courseRepository,
        UnitRepository $unitRepository,
        UserRepository $userRepository,
        ResourceRepository $resourceRepository,
        AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($userRepository);
        $this->courseService = new CourseService($courseRepository);
        $this->unitRepository = $unitRepository;
        $this->unitService = new UnitService($unitRepository);
        $this->resourceRepository = $resourceRepository;
        $this->resourceService = new ResourceService($resourceRepository);
        $this->authorizationService = $authorizationService;
    }

    public function __invoke(CreateVideoResourceRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthor());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $id = $this->resourceRepository->nextIdentity();

        $unitId = new Uuid($request->getUnitId());
        $unit = $this->unitService->findUnit($unitId);

        $status = new Status($request->getStatus());

        $this->resourceService->ensureResourceNotExistsWithThisName($request->getName());

        $resource = new VideoResource(
            $id,
            $unitId,
            $request->getName(),
            $request->getDescription(),
            $request->getContent(),
            $request->getCreated(),
            $request->getModified(),
            $status,
            $request->getVideoUrl(),
            $request->getText()
        );

        $course = $this->courseService->findCourse($unit->getCourseId());
        $this->authorizationService->ensureRequestAuthorHasPermissionsToManageCourse($requestAuthor, $course);

        $this->resourceRepository->save($resource);
    }
}
