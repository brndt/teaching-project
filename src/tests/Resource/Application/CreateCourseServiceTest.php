<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use DateTimeImmutable;
use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateCourseService;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
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
use Test\LaSalle\StudentTeacher\Resource\Builder\CategoryBuilder;
use Test\LaSalle\StudentTeacher\Resource\Builder\CourseBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

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
        $this->createCourseService = new CreateCourseService(
            $this->courseRepository,
            $this->categoryRepository,
            $this->userRepository
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
            ->expects($this->once())
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
            ->expects($this->at(0))
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
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getTeacherId())
            ->willReturn(null);
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

        $this->expectException(PermissionDeniedException::class);
        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getTeacherId())
            ->willReturn($teacher);
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

        $this->expectException(InvalidArgumentException::class);
        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getTeacherId())
            ->willReturn($teacher);
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
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getTeacherId())
            ->willReturn($teacher);
        $this->categoryRepository
            ->expects($this->once())
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

        $this->expectException(InvalidArgumentException::class);
        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getTeacherId())
            ->willReturn($teacher);
        $this->categoryRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($category->getId())
            ->willReturn($category);
        $this->courseRepository
            ->expects($this->once())
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
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getTeacherId())
            ->willReturn($teacher);
        $this->categoryRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($category->getId())
            ->willReturn($category);
        $this->courseRepository
            ->expects($this->once())
            ->method('nextIdentity')
            ->willReturn($course->getId());
        $this->courseRepository
            ->expects($this->once())
            ->method('save')
            ->with($course);
        ($this->createCourseService)($request);
    }
}
