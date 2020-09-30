<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateStudentCoursePermissionRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateTestResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateStudentCoursePermissionService;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateTestResourceStudentAnswerService;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceStudentAnswerRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Builder\CourseBuilder;
use Test\LaSalle\StudentTeacher\Resource\Builder\TestResourceBuilder;
use Test\LaSalle\StudentTeacher\Resource\Builder\UnitBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class CreateTestResourceStudentAnswerServiceTest extends TestCase
{
    private CreateTestResourceStudentAnswerService $createTestResourceStudentAnswerService;
    private $coursePermissionRepository;
    private $userRepository;
    private $unitRepository;
    private $courseRepository;
    private $resourceRepository;
    private AuthorizationService $authorizationService;
    private $resourceStudentAnswerRepository;

    public function setUp(): void
    {
        $this->unitRepository = $this->createMock(UnitRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->resourceRepository = $this->createMock(ResourceRepository::class);
        $this->resourceStudentAnswerRepository = $this->createMock(ResourceStudentAnswerRepository::class);
        $this->coursePermissionRepository = $this->createMock(CoursePermissionRepository::class);
        $this->authorizationService = new AuthorizationService(
            $this->coursePermissionRepository, $this->unitRepository,
            $this->courseRepository
        );

        $this->createTestResourceStudentAnswerService = new CreateTestResourceStudentAnswerService(
            $this->userRepository,
            $this->authorizationService,
            $this->resourceRepository,
            $this->resourceStudentAnswerRepository
        );
    }

    public function testWhenRequestIsValidThenCreateTestResourceStudentAnswer()
    {
        $request = new CreateTestResourceStudentAnswerRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            [
                "0" => [
                    "question" => "hola como estas",
                    "student_assumption" => "bien"
                ],
                "1" => [
                    "question" => "hola que tal",
                    "student_assumption" => "mal"
                ]
            ]
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $resource = (new TestResourceBuilder())
            ->withId(new Uuid($request->getResourceId()))
            ->build();

        $unit = (new UnitBuilder())
            ->withId($resource->getUnitId())
            ->build();

        $course = (new CourseBuilder())
            ->withId($unit->getCourseId())
            ->build();

        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($author);

        $this->resourceRepository
            ->method('ofId')
            ->with(new Uuid($request->getResourceId()))
            ->willReturn($resource);

        $this->unitRepository
            ->method('ofId')
            ->with($resource->getUnitId())
            ->willReturn($unit);

        $this->courseRepository
            ->method('ofId')
            ->with($unit->getCourseId())
            ->willReturn($course);

        ($this->createTestResourceStudentAnswerService)($request);
    }
}
