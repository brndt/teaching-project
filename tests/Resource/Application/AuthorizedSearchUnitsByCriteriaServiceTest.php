<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchUnitsByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateUnitRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\UnitCollectionResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\UnitResponse;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchUnitsByCriteriaService;
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
