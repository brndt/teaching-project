<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

final class CreateCourseService extends CourseService
{
    public function __invoke(CreateCourseRequest $request): void
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $teacherId = $this->createIdFromPrimitive($request->getTeacherId());

        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);

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