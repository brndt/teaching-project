<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use LaSalle\StudentTeacher\Resource\Application\Request\UpdateUnitRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\UnitResponse;
use LaSalle\StudentTeacher\Resource\Application\Service\UpdateUnitService;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
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
use Test\LaSalle\StudentTeacher\Resource\Builder\UnitBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class UpdateUnitServiceTest extends TestCase
{
    private UpdateUnitService $updateUnitService;
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

        $this->updateUnitService = new UpdateUnitService(
            $this->courseRepository,
            $this->userRepository,
            $this->unitRepository,
            $this->authorizationService
        );
    }

    public function testWhenRequestIsValidThenUpdateUnit()
    {
        $request = new UpdateUnitRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'random_name',
            'random_description',
            'random_level',
            'published'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $course = (new CourseBuilder())
            ->withId(new Uuid($request->getCourseId()))
            ->build();

        $unit = (new UnitBuilder())
            ->withId(new Uuid(($request->getUnitId())))
            ->build();

        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);

        $this->courseRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getCourseId())
            ->willReturn($course);

        $this->unitRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getUnitId())
            ->willReturn($unit);

        ($this->updateUnitService)($request);
    }
}
