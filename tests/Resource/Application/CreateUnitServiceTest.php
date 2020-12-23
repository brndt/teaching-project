<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateUnitRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateUnitService;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Builder\CourseBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class CreateUnitServiceTest extends TestCase
{
    private CreateUnitService $createUnitService;
    private $coursePermissionRepository;
    private $userRepository;
    private $unitRepository;
    private $courseRepository;
    private AuthorizationService $authorizationService;

    public function setUp(): void
    {
        $this->unitRepository = $this->createMock(UnitRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->coursePermissionRepository = $this->createMock(CoursePermissionRepository::class);
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->authorizationService = new AuthorizationService(
            $this->coursePermissionRepository, $this->unitRepository,
            $this->courseRepository
        );

        $this->createUnitService = new CreateUnitService(
            $this->unitRepository,
            $this->userRepository,
            $this->courseRepository,
            $this->authorizationService
        );
    }

    public function testWhenRequestIsValidThenCreateUnit()
    {
        $request = new CreateUnitRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'random_name',
            'random_description',
            'random_level',
            new \DateTimeImmutable(),
            null,
            'published'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $course = (new CourseBuilder())
            ->withId(new Uuid($request->getCourseId()))
            ->build();

        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);

        $this->courseRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getCourseId())
            ->willReturn($course);

        $this->unitRepository
            ->expects(self::once())
            ->method('ofName')
            ->with($request->getName())
            ->willReturn(null);

        ($this->createUnitService)($request);
    }
}
