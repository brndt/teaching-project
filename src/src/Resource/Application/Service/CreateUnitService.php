<?php


namespace LaSalle\StudentTeacher\Resource\Application\Service;


use LaSalle\StudentTeacher\Resource\Application\Request\CreateUnitRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

class CreateUnitService
{
    private UserService $userService;
    private UnitRepository $unitRepository;
    private CourseService $courseService;
    private AuthorizationService $authorizationService;

    public function __construct(UnitRepository $unitRepository, UserRepository $userRepository, CourseRepository $courseRepository)
    {
        $this->userService = new UserService($userRepository);
        $this->unitRepository = $unitRepository;
        $this->courseService = new CourseService($courseRepository);
        $this->authorizationService = new AuthorizationService();
    }


    public function __invoke(CreateUnitRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $id = $this->unitRepository->nextIdentity();

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $status = new Status($request->getStatus());

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

        $this->authorizationService->ensureRequestAuthorHasPermissionsToManageCourse($requestAuthor, $course);

        $this->unitRepository->save($unit);
    }
}