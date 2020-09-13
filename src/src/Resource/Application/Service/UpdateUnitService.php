<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\UpdateUnitRequest;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class UpdateUnitService
{
    private AuthorizationService $authorizationService;
    private CourseRepository $courseRepository;
    private CourseService $courseService;
    private UserService $userService;
    private UnitRepository $unitRepository;
    private UnitService $unitService;

    public function __construct(
        CourseRepository $courseRepository,
        UserRepository $userRepository,
        UnitRepository $unitRepository,
        AuthorizationService $authorizationService
    ) {
        $this->courseRepository = $courseRepository;
        $this->unitRepository = $unitRepository;
        $this->courseService = new CourseService($courseRepository);
        $this->userService = new UserService($userRepository);
        $this->unitService = new UnitService($unitRepository);
        $this->authorizationService = $authorizationService;
    }

    public function __invoke(UpdateUnitRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $unitId = new Uuid($request->getUnitId());
        $unit = $this->unitService->findUnit($unitId);

        $this->authorizationService->ensureRequestAuthorHasPermissionsToManageCourse($requestAuthor, $course);

        $unit->setCourseId($courseId);
        $unit->setDescription($request->getDescription());
        $unit->setLevel($request->getLevel());
        $unit->setName($request->getName());
        $unit->setStatus(new Status($request->getStatus()));
        $unit->setModified(new \DateTimeImmutable());

        $this->unitRepository->save($unit);
    }
}
