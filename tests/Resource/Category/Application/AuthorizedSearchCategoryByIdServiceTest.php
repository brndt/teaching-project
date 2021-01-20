<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Category\Application;

use LaSalle\StudentTeacher\Resource\Category\Application\Request\AuthorizedSearchCategoryByIdRequest;
use LaSalle\StudentTeacher\Resource\Category\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Category\Application\Service\AuthorizedSearchCategoryByIdService;
use LaSalle\StudentTeacher\Resource\Category\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Category\Domain\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Category\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
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

final class AuthorizedSearchCategoryByIdServiceTest extends TestCase
{
    private AuthorizedSearchCategoryByIdService $searchCategoryByIdService;
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

        $this->searchCategoryByIdService = new AuthorizedSearchCategoryByIdService(
            $this->categoryRepository,
            $this->userRepository,
            $authorizationService
        );
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new AuthorizedSearchCategoryByIdRequest(
            Uuid::generate()->toString() . '-invalid',
            Uuid::generate()->toString(),
        );

        $this->expectException(InvalidUuidException::class);
        ($this->searchCategoryByIdService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $request = new AuthorizedSearchCategoryByIdRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
        );
        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn(null);
        ($this->searchCategoryByIdService)($request);
    }

    public function testWhenRequestAuthorIsNotAdminThenThrowException()
    {
        $request = new AuthorizedSearchCategoryByIdRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
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
        ($this->searchCategoryByIdService)($request);
    }

    public function testWhenCategoryDoesntExistThenThrowException()
    {
        $request = new AuthorizedSearchCategoryByIdRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
        );
        $this->expectException(CategoryNotFound::class);

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
            ->willReturn(null);
        ($this->searchCategoryByIdService)($request);
    }

    public function testWhenRequestIsValidThenReturnCategory()
    {
        $request = new AuthorizedSearchCategoryByIdRequest(
            Uuid::generate()->toString(),
            Uuid::generate()->toString(),
        );
        $category = (new CategoryBuilder())->build();
        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $expectedCategoryResponse = $this->buildResponse($category);
        $this->userRepository
            ->expects(self::once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects(self::once())
            ->method('ofId')
            ->willReturn($category);

        $actualCategoryResponse = ($this->searchCategoryByIdService)($request);
        $this->assertEquals($expectedCategoryResponse, $actualCategoryResponse);
    }

    private function buildResponse(Category $category): CategoryResponse
    {
        return new CategoryResponse(
            $category->getId()->toString(),
            $category->getName(),
            $category->getStatus()->value(),
        );
    }

}
