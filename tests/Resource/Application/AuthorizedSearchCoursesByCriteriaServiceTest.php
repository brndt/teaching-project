<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use LaSalle\StudentTeacher\Resource\Course\Application\AuthorizedSearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Course\Application\AuthorizedSearchCoursesByCriteriaService;
use LaSalle\StudentTeacher\Resource\Course\Application\CourseCollectionResponse;
use LaSalle\StudentTeacher\Resource\Course\Application\CourseResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Builder\CourseBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class AuthorizedSearchCoursesByCriteriaServiceTest extends TestCase
{
    private AuthorizedSearchCoursesByCriteriaService $searchCoursesByCriteriaService;
    private MockObject $courseRepository;
    private MockObject $userRepository;

    public function setUp(): void
    {
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->searchCoursesByCriteriaService = new AuthorizedSearchCoursesByCriteriaService(
            $this->courseRepository,
            $this->userRepository
        );
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new AuthorizedSearchCoursesByCriteriaRequest(
            Uuid::generate()->toString() . '-invalid',
            null,
            null,
            null,
            null,
            null,
            null
        );

        $this->expectException(InvalidUuidException::class);
        ($this->searchCoursesByCriteriaService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new AuthorizedSearchCoursesByCriteriaRequest(
            Uuid::generate()->toString(),
            null,
            null,
            null,
            null,
            null,
            null
        );

        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn(null);
        ($this->searchCoursesByCriteriaService)($request);
    }

    public function testWhenRequestAuthorRoleIsNotAdminOrTeacherThenThrowException()
    {
        $request = new AuthorizedSearchCoursesByCriteriaRequest(
            Uuid::generate()->toString(),
            null,
            null,
            null,
            null,
            null,
            null
        );

        $requestAuthor = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::STUDENT]))
            ->build();

        $this->expectException(PermissionDeniedException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($requestAuthor);
        ($this->searchCoursesByCriteriaService)($request);
    }

    public function testWhenUserIdIsNotNullAndIsInvalidThenThrowException()
    {
        $request = new AuthorizedSearchCoursesByCriteriaRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString() . '-invalid',
            null,
            null,
            null,
            null,
            null
        );

        $requestAuthor = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $this->expectException(InvalidUuidException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($requestAuthor);
        ($this->searchCoursesByCriteriaService)($request);
    }

    public function testWhenCoursesNotFoundThenReturnEmptyArray()
    {
        $request = new AuthorizedSearchCoursesByCriteriaRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            null,
            null,
            null,
            null,
            null
        );
        $expectedCourseCollectionResponse = new CourseCollectionResponse(...$this->buildResponse(...[]));
        $requestAuthor = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($requestAuthor);
        $this->courseRepository
            ->expects(self::once())
            ->method('matching')
            ->willReturn([]);
        $actualCourseCollectionResponse = ($this->searchCoursesByCriteriaService)($request);
        $this->assertEquals($expectedCourseCollectionResponse, $actualCourseCollectionResponse);
    }

    public function testWhenRequestIsValidThenReturnCourses()
    {
        $request = new AuthorizedSearchCoursesByCriteriaRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            null,
            null,
            null,
            null,
            null
        );

        $requestAuthor = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $courses = [(new CourseBuilder())->build(), (new CourseBuilder())->build()];
        $expectedCourseCollectionResponse = new CourseCollectionResponse(...$this->buildResponse(...$courses));

        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($requestAuthor);
        $this->courseRepository
            ->expects(self::once())
            ->method('matching')
            ->willReturn($courses);
        $actualCourseCollectionResponse = ($this->searchCoursesByCriteriaService)($request);
        $this->assertEquals($expectedCourseCollectionResponse, $actualCourseCollectionResponse);
    }

    private function buildResponse(Course ...$courses): array
    {
        return array_map(
            static function (Course $course) {
                return new CourseResponse(
                    $course->getId()->toString(),
                    $course->getTeacherId()->toString(),
                    $course->getCategoryId()->toString(),
                    $course->getName(),
                    $course->getDescription(),
                    $course->getLevel(),
                    $course->getCreated(),
                    $course->getModified(),
                    $course->getStatus()->value(),
                );
            },
            $courses
        );
    }
}
