<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchStudentCoursePermissionRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CoursePermissionResponse;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CoursePermissionService;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class AuthorizedSearchStudentCoursePermissionService
{
    private UserService $userService;
    private CourseService $courseService;
    private AuthorizationService $authorizationService;
    private CoursePermissionService $coursePermissionService;

    public function __construct(
        CourseRepository $courseRepository,
        UserRepository $userRepository,
        CoursePermissionRepository $repository,
        AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($userRepository);
        $this->courseService = new CourseService($courseRepository);
        $this->authorizationService = $authorizationService;
        $this->coursePermissionService = new CoursePermissionService($repository);
    }

    public function __invoke(AuthorizedSearchStudentCoursePermissionRequest $request): CoursePermissionResponse
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $studentId = new Uuid($request->getStudentId());
        $this->userService->findUser($studentId);

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        $coursePermission = $this->coursePermissionService->findCoursePermission($courseId, $studentId);

        return new CoursePermissionResponse(
            $coursePermission->getId()->toString(),
            $coursePermission->getCourseId()->toString(),
            $coursePermission->getStudentId()->toString(),
            $coursePermission->getCreated(),
            $coursePermission->getModified(),
            $coursePermission->getUntil(),
            $coursePermission->getStatus()->value()
        );
    }
}
