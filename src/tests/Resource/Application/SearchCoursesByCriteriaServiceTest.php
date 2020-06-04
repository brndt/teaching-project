<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Application\Exception\CourseNotFoundException;
use LaSalle\StudentTeacher\Resource\Application\Request\SearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseCollectionResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseResponse;
use LaSalle\StudentTeacher\Resource\Application\Service\SearchCoursesByCriteriaService;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Builder\CourseBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class SearchCoursesByCriteriaServiceTest extends TestCase
{
    private SearchCoursesByCriteriaService $searchCoursesByCriteriaService;
    private MockObject $courseRepository;
    private MockObject $categoryRepository;
    private MockObject $userRepository;

    public function setUp(): void
    {
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->searchCoursesByCriteriaService = new SearchCoursesByCriteriaService(
            $this->courseRepository,
            $this->categoryRepository,
            $this->userRepository
        );
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new SearchCoursesByCriteriaRequest(
            Uuid::generate()->toString() . '-invalid',
            null,
            null,
            null,
            null,
            null,
            null
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->searchCoursesByCriteriaService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new SearchCoursesByCriteriaRequest(
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
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn(null);
        ($this->searchCoursesByCriteriaService)($request);
    }

    public function testWhenRequestAuthorRoleIsNotAdminOrTeacherThenThrowException()
    {
        $request = new SearchCoursesByCriteriaRequest(
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
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($requestAuthor);
        ($this->searchCoursesByCriteriaService)($request);
    }

    public function testWhenUserIdIsNotNullAndIsInvalidThenThrowException()
    {
        $request = new SearchCoursesByCriteriaRequest(
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

        $this->expectException(InvalidArgumentException::class);
        $this->userRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($requestAuthor);
        ($this->searchCoursesByCriteriaService)($request);
    }

    public function testWhenCoursesNotFoundThenReturnEmptyResult()
    {
        $request = new SearchCoursesByCriteriaRequest(
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
        $expectedCourseCollectionResponse = new CourseCollectionResponse(...$this->buildResponse(...[]));

        $this->userRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($requestAuthor);
        $this->courseRepository
            ->expects($this->once())
            ->method('matching')
            ->willReturn([]);
        $actualCourseCollectionResponse = ($this->searchCoursesByCriteriaService)($request);
        $this->assertEquals($expectedCourseCollectionResponse, $actualCourseCollectionResponse);
    }

    public function testWhenRequestIsValidThenReturnCourses()
    {
        $request = new SearchCoursesByCriteriaRequest(
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
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($requestAuthor);
        $this->courseRepository
            ->expects($this->once())
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
