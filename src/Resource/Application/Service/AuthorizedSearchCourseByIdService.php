<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCourseByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class AuthorizedSearchCourseByIdService
{
    private CourseService $courseService;
    private UserService $userService;

    public function __construct(
        CourseRepository $courseRepository,
        UserRepository $userRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->courseService = new CourseService($courseRepository);
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(AuthorizedSearchCourseByIdRequest $request): CourseResponse
    {
        $authorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($authorId);

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        return $this->buildResponse($course);
    }

    private function buildResponse(Course $course): CourseResponse
    {
        return new CourseResponse(
            $course->getId()->toString(),
            $course->getTeacherId()->toString(),
            $course->getCategoryId()->toString(),
            $course->getName(),
            $course->getDescription(),
            $course->getLevel(),
            $course->getCreated(),
            $course->getModified(),
            $course->getStatus()->value(),
        );
    }

}
