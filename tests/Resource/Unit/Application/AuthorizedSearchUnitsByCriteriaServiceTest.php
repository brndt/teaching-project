<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Unit\Application;

use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Unit\Application\Request\AuthorizedSearchUnitsByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Unit\Application\Response\UnitCollectionResponse;
use LaSalle\StudentTeacher\Resource\Unit\Application\Response\UnitResponse;
use LaSalle\StudentTeacher\Resource\Unit\Application\Service\AuthorizedSearchUnitsByCriteriaService;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Aggregate\Unit;
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

final class AuthorizedSearchUnitsByCriteriaServiceTest extends TestCase
{
    private AuthorizedSearchUnitsByCriteriaService $authorizedSearchUnitsByCriteriaService;
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

        $this->authorizedSearchUnitsByCriteriaService = new AuthorizedSearchUnitsByCriteriaService(
            $this->courseRepository,
            $this->unitRepository,
            $this->userRepository,
            $this->authorizationService
        );
    }

    public function testWhenRequestIsValidThenSearchUnits()
    {
        $request = new AuthorizedSearchUnitsByCriteriaRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            null,
            null,
            null,
            null,
            null
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $course = (new CourseBuilder())
            ->withId(new Uuid($request->getCourseId()))
            ->build();

        $units = [(new UnitBuilder())->build(), (new UnitBuilder())->build()];
        $expectedUnitCollectionResponse = new UnitCollectionResponse(...$this->buildResponse(...$units));

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
            ->method('matching')
            ->willReturn($units);

        $actualUnitCollectionResponse = ($this->authorizedSearchUnitsByCriteriaService)($request);
        $this->assertEquals($expectedUnitCollectionResponse, $actualUnitCollectionResponse);
    }


    private function buildResponse(Unit ...$units): array
    {
        return array_map(
            static function (Unit $unit) {
                return new UnitResponse(
                    $unit->getId()->toString(),
                    $unit->getCourseId()->toString(),
                    $unit->getName(),
                    $unit->getDescription(),
                    $unit->getLevel(),
                    $unit->getCreated(),
                    $unit->getModified(),
                    $unit->getStatus()->value(),
                );
            },
            $units
        );
    }

}
