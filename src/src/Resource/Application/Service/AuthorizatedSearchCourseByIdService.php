<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizatedSearchCategoryByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizatedSearchCourseByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;

final class AuthorizatedSearchCourseByIdService extends CourseService
{
    public function __invoke(AuthorizatedSearchCourseByIdRequest $request): CourseResponse
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $course = $this->courseRepository->ofId($this->createIdFromPrimitive($request->getCourseId()));
        $this->ensureCourseExists($course);

        $this->ensureRequestAuthorHasPermissionsToCourse($requestAuthor, $course);

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
