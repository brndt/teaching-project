<?php
declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;


use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchUnitByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\UnitResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class AuthorizedSearchUnitByIdService
{
    private AuthorizationService $authorizationService;
    private CourseService $courseService;
    private UnitService $unitService;
    private UserService $userService;

    public function __construct(CourseRepository $courseRepository, UnitRepository $unitRepository,UserRepository $userRepository, AuthorizationService $authorizationService)
    {
        $this->courseService = new CourseService($courseRepository);
        $this->unitService = new UnitService($unitRepository);
        $this->userService = new UserService($userRepository);
        $this->authorizationService = $authorizationService;
    }

    public function __invoke(AuthorizedSearchUnitByIdRequest $request): UnitResponse
    {
        $authorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($authorId);

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $unitId = new Uuid($request->getUnitId());
        $unit = $this->unitService->findUnit($unitId);

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        return $this->buildResponse($unit);
    }

    private function buildResponse(Unit $unit): UnitResponse
    {
        return new UnitResponse(
            $unit->getId()->toString(),
            $unit->getCourseId()->toString(),
            $unit->getName(),
            $unit->getDescription(),
            $unit->getLevel(),
            $unit->getCreated(),
            $unit->getModified(),
            $unit->getStatus()->value(),
        );
    }
}
