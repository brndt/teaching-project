<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Unit\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Course\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Unit\Application\Response\UpdateUnitRequest;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class UpdateUnitService
{
    private CourseService $courseService;
    private UserService $userService;
    private UnitService $unitService;

    public function __construct(
        private CourseRepository $courseRepository,
        UserRepository $userRepository,
        private UnitRepository $unitRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->courseService = new CourseService($courseRepository);
        $this->userService = new UserService($userRepository);
        $this->unitService = new UnitService($unitRepository);
    }

    public function __invoke(UpdateUnitRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $unitId = new Uuid($request->getUnitId());
        $unit = $this->unitService->findUnit($unitId);

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        $unit->setCourseId($courseId);
        $unit->setDescription($request->getDescription());
        $unit->setLevel($request->getLevel());
        $unit->setName($request->getName());
        $unit->setStatus(new Status($request->getStatus()));
        $unit->setModified(new DateTimeImmutable());

        $this->unitRepository->save($unit);
    }
}
