<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Service;

use LaSalle\StudentTeacher\Resource\Course\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Course\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Exception\CoursePermissionNotFound;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Service\CoursePermissionService;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Aggregate\Resource;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

class AuthorizationService
{
    private CoursePermissionService $coursePermissionService;
    private UnitService $unitService;
    private CourseService $courseService;

    public function __construct(
        CoursePermissionRepository $coursePermissionRepository,
        UnitRepository $unitRepository,
        CourseRepository $courseRepository
    ) {
        $this->coursePermissionService = new CoursePermissionService($coursePermissionRepository);
        $this->courseService = new CourseService($courseRepository);
        $this->unitService = new UnitService($unitRepository);
    }

    public function ensureRequestAuthorIsCertainUser(User $requestAuthor, User $certainUser)
    {
        if (false === $requestAuthor->idEqualsTo($certainUser->getId())) {
            throw new PermissionDeniedException();
        }
    }

    public function ensureRequestAuthorIsOneOfUsers(User $author, User $firstUser, User $secondUser): void
    {
        if (false === $author->idEqualsTo($firstUser->getId()) && false === $author->idEqualsTo($secondUser->getId())) {
            throw new PermissionDeniedException();
        }
    }

    public function ensureRequestAuthorHasPermissionsToUserConnection(User $author, User $user): void
    {
        if (false === $author->isInRole(new Role('admin')) && false === $author->idEqualsTo($user->getId())) {
            throw new PermissionDeniedException();
        }
    }

    public function ensureUserHasPermissionsToManageCourse(User $user, Course $course): void
    {
        if (false === $this->validateUserPermissionToManageCourse($user, $course)) {
            throw new PermissionDeniedException();
        }
    }

    private function validateUserPermissionToManageCourse(User $requestAuthor, Course $course): bool
    {
        if (true === $requestAuthor->isInRole(new Role(Role::ADMIN))) {
            return true;
        }

        if (true === $requestAuthor->idEqualsTo($course->getTeacherId())) {
            return true;
        }

        return false;
    }

    public function ensureRequestAuthorHasPermissionsToCreateCourse(User $requestAuthor, User $user): void
    {
        if (false === $this->validateAuthorPermissionToCreateCourse($requestAuthor, $user)) {
            throw new PermissionDeniedException();
        }
    }

    private function validateAuthorPermissionToCreateCourse(User $requestAuthor, User $user): bool
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

    public function ensureRequestAuthorIsAdmin(User $requestAuthor): void
    {
        if (false === $requestAuthor->getRoles()->contains(new Role(Role::ADMIN))) {
            throw new PermissionDeniedException();
        }
    }

    public function ensureUserHasAccessToResource(User $user, Resource $resource): void
    {
        $unit = $this->unitService->findUnit($resource->getUnitId());
        $course = $this->courseService->findCourse($unit->getCourseId());

        if (
            false === $this->validateStudentPermissionToCourse($user, $resource)
            &&
            false === $this->validateUserPermissionToManageCourse($user, $course)) {
            throw new PermissionDeniedException();
        }
    }

    private function validateStudentPermissionToCourse(User $user, Resource $resourceId): bool
    {
        $unit = $this->unitService->findUnit($resourceId->getUnitId());
        try {
            $this->coursePermissionService->findCoursePermission($unit->getCourseId(), $user->getId());
        } catch (CoursePermissionNotFound) {
            return false;
        }
        return true;
    }
}
