<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Category\Application;

use LaSalle\StudentTeacher\Resource\Category\Application\Request\UpdateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Category\Application\Service\UpdateCategoryService;
use LaSalle\StudentTeacher\Resource\Category\Domain\Exception\CategoryAlreadyExists;
use LaSalle\StudentTeacher\Resource\Category\Domain\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Category\Domain\Repository\CategoryRepository;
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
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

final class UpdateCategoryServiceTest extends TestCase
{
    private UpdateCategoryService $updateCategoryService;
    private MockObject $categoryRepository;
    private MockObject $userRepository;

    public function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $coursePermissionRepository = $this->createMock(CoursePermissionRepository::class);
        $unitRepository = $this->createMock(UnitRepository::class);
        $courseRepository = $this->createMock(CourseRepository::class);
        $authorizationService = new AuthorizationService($coursePermissionRepository, $unitRepository, $courseRepository);

        $this->updateCategoryService = new UpdateCategoryService($this->userRepository, $this->categoryRepository, $authorizationService);
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new UpdateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138-invalid',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-new-category-name',
            'published'
        );

        $this->expectException(InvalidUuidException::class);
        ($this->updateCategoryService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $request = new UpdateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-new-category-name',
            'published'
        );

        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn(null);
        ($this->updateCategoryService)($request);
    }

    public function testWhenRequestAuthorIsNotAdminThenThrowException()
    {
        $request = new UpdateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-new-category-name',
            'published'
        );

        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::STUDENT]))
            ->build();
        $this->expectException(PermissionDeniedException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        ($this->updateCategoryService)($request);
    }

    public function testWhenCategoryIdIsInvalidThenThrowException()
    {
        $request = new UpdateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138-invalid',
            'some-new-category-name',
            'published'
        );

        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $this->expectException(InvalidUuidException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        ($this->updateCategoryService)($request);
    }

    public function testWhenCategoryIsNotFoundThenThrowException()
    {
        $request = new UpdateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-new-category-name',
            'published'
        );

        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $this->expectException(CategoryNotFound::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getCategoryId()))
            ->willReturn(null);
        ($this->updateCategoryService)($request);
    }

    public function testWhenNewCategoryNameIsNotAvailableThenThrowException()
    {
        $request = new UpdateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-new-category-name',
            'published'
        );
        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();
        $categoryOfName = (new CategoryBuilder())
            ->withName($request->getNewName())
            ->build();

        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $this->expectException(CategoryAlreadyExists::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getCategoryId()))
            ->willReturn($category);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofName')
            ->with($request->getNewName())
            ->willReturn($categoryOfName);
        ($this->updateCategoryService)($request);
    }

    public function testWhenNewCategoryStatusIsInvalidThenThrowException()
    {
        $request = new UpdateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-new-category-name',
            'invalid-status'
        );
        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();

        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $this->expectException(InvalidStatusException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getCategoryId()))
            ->willReturn($category);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofName')
            ->with($request->getNewName())
            ->willReturn(null);
        ($this->updateCategoryService)($request);
    }

    public function testWhenRequestIsValidThenUpdateCategory()
    {
        $request = new UpdateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-new-category-name',
            'published'
        );
        $category = (new CategoryBuilder())
            ->withId(new Uuid($request->getCategoryId()))
            ->build();

        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getCategoryId()))
            ->willReturn($category);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofName')
            ->with($request->getNewName())
            ->willReturn(null);
        $this->categoryRepository
            ->expects(self::once())
            ->method('save')
            ->with($category);
        ($this->updateCategoryService)($request);
    }

}
