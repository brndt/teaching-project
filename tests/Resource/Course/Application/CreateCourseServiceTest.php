<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Course\Application;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Category\Domain\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Category\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Course\Application\Request\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Course\Application\Service\CreateCourseService;
use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidStatusException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Shared\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Category\Domain\CategoryBuilder;
use Test\LaSalle\StudentTeacher\Resource\Course\Domain\CourseBuilder;
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

final class CreateCourseServiceTest extends TestCase
{
    private CreateCourseService $createCourseService;
    private MockObject $courseRepository;
    private MockObject $categoryRepository;
    private MockObject $userRepository;

    public function setUp(): void
    {
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $coursePermissionRepository = $this->createMock(CoursePermissionRepository::class);
        $unitRepository = $this->createMock(UnitRepository::class);
        $courseRepository = $this->createMock(CourseRepository::class);
        $authorizationService = new AuthorizationService($coursePermissionRepository, $unitRepository, $courseRepository);

        $this->createCourseService = new CreateCourseService(
            $this->categoryRepository,
            $this->courseRepository,
            $this->userRepository,
            $authorizationService
        );
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString() . '-invalid',
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'published',
        );

        $this->expectException(InvalidUuidException::class);
        ($this->createCourseService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'published',
        );

        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn(null);
        ($this->createCourseService)($request);
    }

    public function testWhenTeacherIdIsInvalidThenThrowException()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString() . '-invalid',
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'published',
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

        ($this->createCourseService)($request);
    }

    public function testWhenTeacherIsNotFoundThenThrowException()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(UserNotFoundException::class);

        $this->userRepository
            ->method('ofId')
            ->withConsecutive([$request->getRequestAuthorId()], [$request->getTeacherId()])
            ->willReturn($author, null);

        ($this->createCourseService)($request);
    }

    public function testWhenRequestAuthorHasntPermissionsThenThrowException()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::STUDENT]))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();

        $this->expectException(PermissionDeniedException::class);

        $this->userRepository
            ->method('ofId')
            ->withConsecutive([$request->getRequestAuthorId()], [$request->getTeacherId()])
            ->willReturn($author, $teacher);

        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($request->getCategoryId())
            ->willReturn($category);

        ($this->createCourseService)($request);
    }

    public function testWhenCategoryIdIsInvalidThenThrowException()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString() . '-invalid',
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(InvalidUuidException::class);

        $this->userRepository
            ->method('ofId')
            ->withConsecutive([$request->getRequestAuthorId()], [$request->getTeacherId()])
            ->willReturn($author, $teacher);

        ($this->createCourseService)($request);
    }

    public function testWhenCategoryNotFoundThenThrowException()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();

        $this->expectException(CategoryNotFound::class);

        $this->userRepository
            ->method('ofId')
            ->withConsecutive([$request->getRequestAuthorId()], [$request->getTeacherId()])
            ->willReturn($author, $teacher);

        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($category->getId())
            ->willReturn(null);

        ($this->createCourseService)($request);
    }

    public function testWhenCategoryStatusIsInvalidThenThrowException()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'invalid-published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();

        $this->expectException(InvalidStatusException::class);

        $this->userRepository
            ->method('ofId')
            ->withConsecutive([$request->getRequestAuthorId()], [$request->getTeacherId()])
            ->willReturn($author, $teacher);

        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($category->getId())
            ->willReturn($category);
        $this->courseRepository
            ->expects(self::once())
            ->method('nextIdentity')
            ->willReturn(Uuid::generate());
        ($this->createCourseService)($request);
    }

    public function testWhenRequestIsValidThenCreateCourse()
    {
        $request = new CreateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            new DateTimeImmutable(),
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();

        $course = (new CourseBuilder())
            ->withCategoryId(new Uuid($request->getCategoryId()))
            ->withTeacherId(new Uuid($request->getTeacherId()))
            ->withName($request->getName())
            ->withDescription($request->getDescription())
            ->withLevel($request->getLevel())
            ->withCreated($request->getCreated())
            ->withModified($request->getModified())
            ->build();

        $this->userRepository
            ->method('ofId')
            ->withConsecutive([$request->getRequestAuthorId()], [$request->getTeacherId()])
            ->willReturn($author, $teacher);

        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->with($category->getId())
            ->willReturn($category);
        $this->courseRepository
            ->expects(self::once())
            ->method('nextIdentity')
            ->willReturn($course->getId());
        $this->courseRepository
            ->expects(self::once())
            ->method('save')
            ->with($course);
        ($this->createCourseService)($request);
    }
}
