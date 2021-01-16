<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use LaSalle\StudentTeacher\Resource\Category\Application\CreateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Category\Application\CreateCategoryService;
use LaSalle\StudentTeacher\Resource\Category\Application\Exception\CategoryAlreadyExists;
use LaSalle\StudentTeacher\Resource\Domain\Exception\InvalidStatusException;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Builder\CategoryBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class CreateCategoryServiceTest extends TestCase
{
    private CreateCategoryService $createCategoryService;
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

        $this->createCategoryService = new CreateCategoryService($this->categoryRepository, $this->userRepository, $authorizationService);
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new CreateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138-invalid',
            'some-category-name',
            'published'
        );

        $this->expectException(InvalidUuidException::class);
        ($this->createCategoryService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $request = new CreateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-category-name',
            'published'
        );
        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn(null);
        ($this->createCategoryService)($request);
    }

    public function testWhenRequestAuthorIsNotAdminThenThrowException()
    {
        $request = new CreateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-category-name',
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
        ($this->createCategoryService)($request);
    }

    public function testWhenCategoryAlreadyExistsWithThisNameThenThrowException()
    {
        $request = new CreateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-category-name',
            'published'
        );
        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $category = (new CategoryBuilder())
            ->withName($request->getCategoryName())
            ->withStatus(new Status($request->getCategoryStatus()))
            ->build();
        $this->expectException(CategoryAlreadyExists::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofName')
            ->with($request->getCategoryName())
            ->willReturn($category);
        ($this->createCategoryService)($request);
    }

    public function testWhenCategoryStatusIsInvalidThenThrowException()
    {
        $request = new CreateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-category-name',
            'invalid-status'
        );
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
            ->method('ofName')
            ->with($request->getCategoryName())
            ->willReturn(null);
        ($this->createCategoryService)($request);
    }

    public function testWhenRequestIsValidThenThrowException()
    {
        $request = new CreateCategoryRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'some-category-name',
            'published'
        );
        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $category = (new CategoryBuilder())
            ->withName($request->getCategoryName())
            ->withStatus(new Status($request->getCategoryStatus()))
            ->build();
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofName')
            ->with($request->getCategoryName())
            ->willReturn(null);
        $this->categoryRepository
            ->expects(self::once())
            ->method('nextIdentity')
            ->willReturn($category->getId());
        $this->categoryRepository
            ->expects(self::once())
            ->method('save')
            ->with($category);
        ($this->createCategoryService)($request);
    }


}
