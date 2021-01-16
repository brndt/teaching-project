<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Application;

use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Application\Request\CreateTestResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Application\Service\CreateTestResourceStudentAnswerService;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\Repository\ResourceStudentAnswerRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Course\Domain\CourseBuilder;
use Test\LaSalle\StudentTeacher\Resource\Resource\Domain\TestResourceBuilder;
use Test\LaSalle\StudentTeacher\Resource\Unit\Domain\UnitBuilder;
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
            ->expects(self::once())
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
