<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\CoursePermission\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Course\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\CoursePermission\Application\Request\CreateStudentCoursePermissionRequest;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Aggregate\CoursePermission;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Service\CoursePermissionService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;

final class CreateStudentCoursePermissionService
{
    private UserService $userService;
    private CourseService $courseService;
    private CoursePermissionService $coursePermissionService;

    public function __construct(
        CourseRepository $courseRepository,
        UserRepository $userRepository,
        private CoursePermissionRepository $repository,
        private AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($userRepository);
        $this->courseService = new CourseService($courseRepository);
        $this->coursePermissionService = new CoursePermissionService($repository);
    }

    public function __invoke(CreateStudentCoursePermissionRequest $request)
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $studentId = new Uuid($request->getStudentId());
        $this->userService->findUser($studentId);

        $this->coursePermissionService->ensureCoursePermissionNotExists($courseId, $studentId);

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        $coursePermission = new CoursePermission(
            $this->repository->nextIdentity(),
            $courseId,
            $studentId,
            new DateTimeImmutable(),
            null,
            $request->getUntil(),
            new Status($request->getStatus())
        );

        $this->repository->save($coursePermission);
    }
}
