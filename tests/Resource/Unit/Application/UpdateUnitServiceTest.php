<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Unit\Application;

use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Unit\Application\Response\UpdateUnitRequest;
use LaSalle\StudentTeacher\Resource\Unit\Application\Service\UpdateUnitService;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Course\Domain\CourseBuilder;
use Test\LaSalle\StudentTeacher\Resource\Unit\Domain\UnitBuilder;
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

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
            ->method('ofId')
            ->with($request->getUnitId())
            ->willReturn($unit);

        ($this->updateUnitService)($request);
    }
}
