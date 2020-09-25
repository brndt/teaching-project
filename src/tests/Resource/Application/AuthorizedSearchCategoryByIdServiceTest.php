<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCategoryByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchCategoryByIdService;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
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
            ->expects($this->once())
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
            ->expects($this->once())
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
            ->expects($this->once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects($this->once())
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
            ->expects($this->once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($user);
        $this->categoryRepository
            ->expects($this->once())
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
