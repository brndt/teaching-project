<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Exception\CourseNotFoundException;
use LaSalle\StudentTeacher\Resource\Application\Request\UpdateCourseRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\UpdateCourseService;
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
use Test\LaSalle\StudentTeacher\Resource\Builder\CategoryBuilder;
use Test\LaSalle\StudentTeacher\Resource\Builder\CourseBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class UpdateCourseServiceTest extends TestCase
{
    private UpdateCourseService $updateCourseService;
    private MockObject $courseRepository;
    private MockObject $categoryRepository;
    private MockObject $userRepository;

    public function setUp(): void
    {
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->updateCourseService = new UpdateCourseService(
            $this->courseRepository,
            $this->categoryRepository,
            $this->userRepository
        );
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString() . '-invalid',
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            null,
            'published',
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->updateCourseService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            null,
            'published',
        );

        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn(null);
        ($this->updateCourseService)($request);
    }

    public function testWhenTeacherIdIsInvalidThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString() . '-invalid',
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(InvalidArgumentException::class);
        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        ($this->updateCourseService)($request);
    }

    public function testWhenTeacherIsNotFoundThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
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
        ($this->updateCourseService)($request);
    }

    public function testWhenCategoryIdIsInvalidThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString() . '-invalid',
            'course_name',
            'some_description',
            'beginner',
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getTeacherId()))
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
        ($this->updateCourseService)($request);
    }

    public function testWhenCategoryNotFoundThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getTeacherId()))
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
        ($this->updateCourseService)($request);
    }

    public function testWhenCourseIdIsInvalidThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString() . '-invalid',
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getTeacherId()))
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
        ($this->updateCourseService)($request);
    }

    public function testWhenCourseNotFoundThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getTeacherId()))
            ->build();

        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();

        $course = (new CourseBuilder())
            ->withId(new Uuid($request->getId()))
            ->build();

        $this->expectException(CourseNotFoundException::class);
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
            ->method('ofId')
            ->with($course->getId())
            ->willReturn(null);
        ($this->updateCourseService)($request);
    }

    public function testWhenRequestAuthorHasntPermissionsToCourseThenThrowException()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
            null,
            'published',
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::TEACHER]))
            ->build();

        $teacher = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();

        $course = (new CourseBuilder())
            ->withId(new Uuid($request->getId()))
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
        $this->categoryRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($category->getId())
            ->willReturn($category);
        $this->courseRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($course->getId())
            ->willReturn($course);
        ($this->updateCourseService)($request);
    }

    public function testWhenRequestIsValidThenUpdateCourse()
    {
        $request = new UpdateCourseRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
            'course_name',
            'some_description',
            'beginner',
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

        $expectedCourse = (new CourseBuilder())
            ->withId(new Uuid($request->getId()))
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
            ->method('ofId')
            ->with($expectedCourse->getId())
            ->willReturn($expectedCourse);
        $this->courseRepository
            ->expects($this->once())
            ->method('save')
            ->with($expectedCourse);
        ($this->updateCourseService)($request);
    }
}