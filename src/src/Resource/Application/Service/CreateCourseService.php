<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class CreateCourseService extends CourseService
{
    public function __invoke(CreateCourseRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $teacherId = new Uuid($request->getTeacherId());
        $teacher = $this->userService->findUser($teacherId);

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
