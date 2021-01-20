<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Unit\Application\Service;

use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Course\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Unit\Application\Request\CreateUnitRequest;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;

final class CreateUnitService
{
    private UserService $userService;
    private CourseService $courseService;
    private UnitService $unitService;

    public function __construct(
        private UnitRepository $unitRepository,
        UserRepository $userRepository,
        CourseRepository $courseRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($userRepository);
        $this->unitService = new UnitService($unitRepository);
        $this->courseService = new CourseService($courseRepository);
    }

    public function __invoke(CreateUnitRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $id = $this->unitRepository->nextIdentity();

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $status = new Status($request->getStatus());

        $this->unitService->ensureUnitNotExistsWithThisName($request->getName());

        $unit = new Unit(
            $id,
            $courseId,
            $request->getName(),
            $request->getDescription(),
            $request->getLevel(),
            $request->getCreated(),
            $request->getModified(),
            $status
        );

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        $this->unitRepository->save($unit);
    }
}
