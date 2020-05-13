<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\SearchCourseRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseResponse;

final class SearchCourseService extends CourseService
{
    public function __invoke(SearchCourseRequest $request)
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $courseId = $this->createIdFromPrimitive($request->getCourseId());
        $course = $this->courseRepository->ofId($courseId);
        $this->ensureCourseExists($course);

        $this->ensureTeacherHasPermissions($requestAuthor, $course);

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