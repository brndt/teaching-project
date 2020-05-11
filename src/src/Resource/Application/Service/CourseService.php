<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Exception\CourseNotFoundException;
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

abstract class CourseService
{
    protected CourseRepository $courseRepository;
    protected CategoryRepository $categoryRepository;
    protected UserRepository $userRepository;

    public function __construct(
        CourseRepository $courseRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
    }

    protected function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    protected function createStatusFromPrimitive(string $status)
    {
        try {
            return new Status($status);
        } catch (InvalidArgumentException $error) {
            throw new InvalidArgumentException($error->getMessage());
        }
    }

    protected function ensureRequestAuthorHasPermissions(User $requestAuthor, User $user): void
    {
        if (false === $requestAuthor->isInRole(new Role('admin')) &&
            false === $requestAuthor->idEqualsTo($user->getId()) &&
            false === $user->isInRole(new Role('teacher'))) {
            throw new PermissionDeniedException();
        }
    }

    protected function ensureTeacherHasPermissions(User $user, Course $course): void
    {
        if (false === $user->isInRole(new Role('admin')) &&
            false === $user->idEqualsTo($course->getTeacherId())) {
            throw new PermissionDeniedException();
        }
    }

    protected function ensureUserExists(?User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    protected function ensureCategoryExists(?Uuid $categoryId)
    {
        if (null === $this->categoryRepository->ofId($categoryId)) {
            throw new CategoryNotFound();
        }
    }

    protected function ensureCoursesExist(?array $courses)
    {
        if (true === empty($courses)) {
            throw new CourseNotFoundException();
        }
    }

    protected function ensureCourseExists(?Course $course)
    {
        if (null === $course) {
            throw new CourseNotFoundException();
        }
    }
}