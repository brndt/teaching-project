<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\UpdateCourseRequest;

final class UpdateCourseService extends CourseService
{
    public function __invoke(UpdateCourseRequest $request)
    {
        $courseId = $this->createIdFromPrimitive($request->getId());

        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $teacherId = $this->createIdFromPrimitive($request->getTeacherId());

        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

        $teacher = $this->userRepository->ofId($teacherId);
        $this->ensureUserExists($teacher);

        $this->ensureRequestAuthorHasPermissions($requestAuthor, $teacher);

        $categoryId = $this->createIdFromPrimitive($request->getCategoryId());
        $this->ensureCategoryExists($categoryId);

        $course = $this->courseRepository->ofId($courseId);
        $this->ensureCourseExists($course);

        $this->ensureTeacherHasPermissions($teacher, $course);

        $course->setDescription($request->getDescription());
        $course->setLevel($request->getLevel());
        $course->setName($request->getName());
        $course->setCategoryId($categoryId);
        $course->setStatus($this->createStatusFromPrimitive($request->getStatus()));
        $course->setModified($request->getModified());

        $this->courseRepository->save($course);
    }
}