<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;


use LaSalle\StudentTeacher\Resource\Application\Request\CreateResourceRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Resource;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\ResourceType;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class CreateResourceService
{
    private UserService $userService;
    private CourseService $courseService;
    public function __construct(UserRepository $userRepository, CourseRepository $courseRepository)
    {
        $this->userService = new UserService($userRepository);
        $this->courseService = new CourseService($courseRepository);
    }

    public function __invoke(CreateResourceRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $unitId = new Uuid($request->getUnitId());
        $unit = $this->findUnit($unitId);

        $courseId = $unit->getCourseId();
        $course = $this->ensureCourseExists($courseId);

        $teacherId = $course->getTeacherId();
        $teacher = $this->userService->findUser($teacherId);

        $this->ensureRequestAuthorHasPermissions($requestAuthor, $teacher);

        $id = $this->unitRepository->nextIdentity();
        $status = $this->createStatusFromPrimitive($request->getStatus());

        $resourceType = new ResourceType($request->getResourceType());

        $resource = new Resource(
            $id,
            $unitId,
            $request->getName(),
            $request->getDescription(),
            $request->getContent(),
            $resourceType->value(),
            $request->getCreated(),
            $request->getModified(),
            $status
        );
        $this->resourceRepository->save($resource);
    }
}