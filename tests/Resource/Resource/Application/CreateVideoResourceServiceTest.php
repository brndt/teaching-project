<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Resource\Application;

use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Resource\Application\Request\CreateVideoResourceRequest;
use LaSalle\StudentTeacher\Resource\Resource\Application\Service\CreateVideoResourceService;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Repository\ResourceRepository;
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

final class CreateVideoResourceServiceTest extends TestCase
{
    private CreateVideoResourceService $createVideoResourceService;
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

        $this->createVideoResourceService = new CreateVideoResourceService(
            $this->courseRepository,
            $this->unitRepository,
            $this->userRepository,
            $this->resourceRepository,
            $this->authorizationService
        );
    }

    public function testWhenRequestIsValidThenCreateVideoResource()
    {
        $request = new CreateVideoResourceRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'random name',
            'random description',
            'some content',
            'published',
            'video url',
            'video description'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthor()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $unit = (new UnitBuilder())
            ->withId(new Uuid($request->getUnitId()))
            ->build();

        $course = (new CourseBuilder())
            ->withId($unit->getCourseId())
            ->build();

        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthor())
            ->willReturn($author);

        $this->unitRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getUnitId())
            ->willReturn($unit);

        $this->courseRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($unit->getCourseId())
            ->willReturn($course);

        ($this->createVideoResourceService)($request);
    }
}
