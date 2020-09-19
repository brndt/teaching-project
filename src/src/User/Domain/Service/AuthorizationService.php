<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Service;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Resource;
use LaSalle\StudentTeacher\Resource\Domain\Exception\CoursePermissionNotFound;
use LaSalle\StudentTeacher\Resource\Domain\Exception\ResourceNotFoundException;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CoursePermissionService;
use LaSalle\StudentTeacher\Resource\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

class AuthorizationService
{
    private CoursePermissionService $coursePermissionService;
    private UnitService $unitService;

    public function __construct(CoursePermissionRepository $coursePermissionRepository, UnitRepository $unitRepository)
    {
        $this->coursePermissionService = new CoursePermissionService($coursePermissionRepository);
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

    public function ensureRequestAuthorHasPermissionsToManageCourse(User $requestAuthor, Course $course): void
    {
        if (false === $this->validateAuthorPermissionToManageCourse($requestAuthor, $course)) {
            throw new PermissionDeniedException();
        }
    }

    public function ensureRequestAuthorHasPermissionsToCreateCourse(User $requestAuthor, User $user): void
    {
        if (false === $this->validateAuthorPermissionToCreateCourse($requestAuthor, $user)) {
            throw new PermissionDeniedException();
        }
    }

    private function validateAuthorPermissionToManageCourse(User $requestAuthor, Course $course): bool
    {
        if (true === $requestAuthor->isInRole(new Role(Role::ADMIN))) {
            return true;
        }

        if (true === $requestAuthor->idEqualsTo($course->getTeacherId())) {
            return true;
        }

        return false;
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

    public function ensureStudentHasAccessToResource(Uuid $studentId, Resource $resourceId): void
    {
        $unit = $this->unitService->findUnit($resourceId->getUnitId());
        try {
            $this->coursePermissionService->findCoursePermission($unit->getCourseId(), $studentId);
        } catch (CoursePermissionNotFound $exception) {
            throw new PermissionDeniedException();
        }
    }
}
