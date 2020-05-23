<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Exception\CourseNotFoundException;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Exception\InvalidStatusException;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filter;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
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
            throw new InvalidArgumentException($error->getMessage());
        }
    }

    protected function createStatusFromPrimitive(string $status): Status
    {
        try {
            return new Status($status);
        } catch (InvalidStatusException $error) {
            throw new InvalidArgumentException($error->getMessage());
        }
    }

    protected function ensureRequestAuthorHasPermissions(User $requestAuthor, User $user): void
    {
        if (false === $this->validateAuthorPermission($requestAuthor, $user)) {
            throw new PermissionDeniedException();
        }
    }

    private function validateAuthorPermission(User $requestAuthor, User $user): bool
    {
        if (true === $requestAuthor->isInRole(new Role(Role::ADMIN))) {
            return true;
        }
        if (false === $user->isInRole(new Role(Role::TEACHER))) {
            return false;
        }
        if (true === $requestAuthor->idEqualsTo($user->getId())) {
            return true;
        }
        return false;
    }

    protected function ensureRequestAuthorHasPermissionsToCourse(User $user, Course $course): void
    {
        if (false === $this->validateUserCoursePermission($user, $course)) {
            throw new PermissionDeniedException();
        }
    }

    private function validateUserCoursePermission(User $user, Course $course): bool
    {
        if (true === $user->isInRole(new Role(Role::ADMIN))) {
            return true;
        }
        if (true === $user->idEqualsTo($course->getTeacherId())) {
            return true;
        }
        return false;
    }

    protected function ensureUserExists(?User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    protected function ensureCategoryExists(?Uuid $categoryId): void
    {
        if (null === $this->categoryRepository->ofId($categoryId)) {
            throw new CategoryNotFound();
        }
    }

    protected function ensureCoursesExist(?Course...$courses): void
    {
        if (true === empty($courses)) {
            throw new CourseNotFoundException();
        }
    }

    protected function ensureCourseExists(?Course $course): void
    {
        if (null === $course) {
            throw new CourseNotFoundException();
        }
    }

    protected function createFiltersDependingByRoles(User $user): Filters
    {
        {
            if (true === $user->isInRole(new Role(Role::ADMIN))) {
                return Filters::fromValues([]);
            }
            if (true === $user->isInRole(new Role(Role::TEACHER))) {
                return Filters::fromValues(
                    [['field' => 'teacherId', 'operator' => '=', 'value' => $user->getId()->toString()]]
                );
            }
            throw new PermissionDeniedException();
        }
    }

    protected function createFilterByTeacherId(Uuid $userId): Filter
    {
        return Filter::fromValues(['field' => 'teacherId', 'operator' => '=', 'value' => $userId->toString()]);
    }
}