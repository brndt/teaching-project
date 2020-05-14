<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;

final class CreateCourseService extends CourseService
{
    public function __invoke(CreateCourseRequest $request): void
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $teacherId = $this->createIdFromPrimitive($request->getTeacherId());
        $teacher = $this->userRepository->ofId($teacherId);
        $this->ensureUserExists($teacher);

        $this->ensureRequestAuthorHasPermissions($requestAuthor, $teacher);

        $categoryId = $this->createIdFromPrimitive($request->getCategoryId());
        $this->ensureCategoryExists($categoryId);

        $id = $this->courseRepository->nextIdentity();
        $status = $this->createStatusFromPrimitive($request->getStatus());

        $course = new Course(
            $id,
            $teacherId,
            $categoryId,
            $request->getName(),
            $request->getDescription(),
            $request->getLevel(),
            $request->getCreated(),
            $request->getModified(),
            $status
        );
        $this->courseRepository->save($course);
    }
}