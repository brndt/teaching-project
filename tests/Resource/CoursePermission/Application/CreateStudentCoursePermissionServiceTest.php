<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\CoursePermission\Application;

use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Application\Request\CreateStudentCoursePermissionRequest;
use LaSalle\StudentTeacher\Resource\CoursePermission\Application\Service\CreateStudentCoursePermissionService;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Course\Domain\CourseBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class CreateStudentCoursePermissionServiceTest extends TestCase
{
    private CreateStudentCoursePermissionService $createStudentCoursePermissionService;
    private $coursePermissionRepository;
    private $userRepository;
    private $unitRepository;
    private $courseRepository;
    private $resourceRepository;
    private AuthorizationService $authorizationService;

    public function setUp(): void
    {
        $this->unitRepository = $this->createMock(UnitRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->resourceRepository = $this->createMock(ResourceRepository::class);
        $this->coursePermissionRepository = $this->createMock(CoursePermissionRepository::class);
        $this->authorizationService = new AuthorizationService(
            $this->coursePermissionRepository, $this->unitRepository,
            $this->courseRepository
        );

        $this->createStudentCoursePermissionService = new CreateStudentCoursePermissionService(
            $this->courseRepository,
            $this->userRepository,
            $this->coursePermissionRepository,
            $this->authorizationService
        );
    }

    public function testWhenRequestIsValidThenCreateStudentCoursePermission()
    {
        $request = new CreateStudentCoursePermissionRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'published',
            null,
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $course = (new CourseBuilder())
            ->withId(new Uuid($request->getCourseId()))
            ->build();

        $student = (new UserBuilder())
            ->withId(new Uuid($request->getStudentId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::STUDENT]))
            ->build();

        $this->userRepository
            ->method('ofId')
            ->withConsecutive([$request->getRequestAuthorId()], [$request->getStudentId()])
            ->willReturn($author, $student);

        $this->courseRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getCourseId())
            ->willReturn($course);

        ($this->createStudentCoursePermissionService)($request);
    }
}
