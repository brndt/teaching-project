<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Course\Application;

use LaSalle\StudentTeacher\Resource\Course\Application\Request\AuthorizedSearchCourseByIdRequest;
use LaSalle\StudentTeacher\Resource\Course\Application\Response\CourseResponse;
use LaSalle\StudentTeacher\Resource\Course\Application\Service\AuthorizedSearchCourseByIdService;
use LaSalle\StudentTeacher\Resource\Course\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Course\Domain\Exception\CourseNotFoundException;
use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Shared\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Course\Domain\CourseBuilder;
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

final class AuthorizedSearchCourseByIdServiceTest extends TestCase
{
    private AuthorizedSearchCourseByIdService $searchCourseService;
    private MockObject $courseRepository;
    private MockObject $userRepository;

    public function setUp(): void
    {
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $coursePermissionRepository = $this->createMock(CoursePermissionRepository::class);
        $unitRepository = $this->createMock(UnitRepository::class);
        $courseRepository = $this->createMock(CourseRepository::class);
        $authorizationService = new AuthorizationService($coursePermissionRepository, $unitRepository, $courseRepository);

        $this->searchCourseService = new AuthorizedSearchCourseByIdService(
            $this->courseRepository,
            $this->userRepository,
            $authorizationService
        );
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new AuthorizedSearchCourseByIdRequest(
            Uuid::generate()->toString() . '-invalid',
            Uuid::generate()->toString(),
        );

        $this->expectException(InvalidUuidException::class);
        ($this->searchCourseService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new AuthorizedSearchCourseByIdRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
        );

        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn(null);
        ($this->searchCourseService)($request);
    }

    public function testWhenCourseIdIsInvalidThenThrowException()
    {
        $request = new AuthorizedSearchCourseByIdRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString() . '-invalid',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(InvalidUuidException::class);

        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);

        ($this->searchCourseService)($request);
    }

    public function testWhenCourseIsNotFoundThenThrowException()
    {
        $request = new AuthorizedSearchCourseByIdRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(CourseNotFoundException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->courseRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getCourseId()))
            ->willReturn(null);
        ($this->searchCourseService)($request);
    }

    public function testWhenRequestIsValidThenSearchCourse()
    {
        $request = new AuthorizedSearchCourseByIdRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $course = (new CourseBuilder())
            ->withId(new Uuid($request->getCourseId()))
            ->build();
        $expectedCourseResponse = $this->buildResponse($course);

        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->courseRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($course->getId())
            ->willReturn($course);
        $actualCourseResponse = ($this->searchCourseService)($request);
        $this->assertEquals($expectedCourseResponse, $actualCourseResponse);
    }

    private function buildResponse(Course $course)
    {
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
    }
}
