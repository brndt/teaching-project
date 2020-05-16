<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\UpdateCourseRequest;

final class UpdateCourseService extends CourseService
{
    public function __invoke(UpdateCourseRequest $request): void
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $teacherId = $this->createIdFromPrimitive($request->getTeacherId());
        $teacher = $this->userRepository->ofId($teacherId);
        $this->ensureUserExists($teacher);

        $categoryId = $this->createIdFromPrimitive($request->getCategoryId());
        $this->ensureCategoryExists($categoryId);

        $courseId = $this->createIdFromPrimitive($request->getId());
        $course = $this->courseRepository->ofId($courseId);
        $this->ensureCourseExists($course);

        $this->ensureRequestAuthorHasPermissionsToCourse($requestAuthor, $course);

        $course->setDescription($request->getDescription());
        $course->setLevel($request->getLevel());
        $course->setName($request->getName());
        $course->setCategoryId($categoryId);
        $course->setStatus($this->createStatusFromPrimitive($request->getStatus()));
        $course->setModified($request->getModified());

        $this->courseRepository->save($course);
    }
}