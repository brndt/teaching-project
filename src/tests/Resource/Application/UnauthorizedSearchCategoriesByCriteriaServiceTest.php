<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCategoriesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\UnauthorizedSearchCategoriesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CategoryCollectionResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchCategoriesByCriteria;
use LaSalle\StudentTeacher\Resource\Application\Service\UnauthorizedSearchCategoriesByCriteriaService;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Builder\CategoryBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class UnauthorizedSearchCategoriesByCriteriaServiceTest extends TestCase
{
    private UnauthorizedSearchCategoriesByCriteriaService $searchCategoriesByCriteria;
    private MockObject $categoryRepository;
    private MockObject $userRepository;

    public function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->searchCategoriesByCriteria = new UnauthorizedSearchCategoriesByCriteriaService(
            $this->categoryRepository,
            $this->userRepository
        );
    }

    public function testWhenCategoriesDontExistThenThrowException()
    {
        $request = new UnauthorizedSearchCategoriesByCriteriaRequest(
            [],
            null,
            null,
            null,
            null,
            null
        );
        $this->expectException(CategoryNotFound::class);

        $user = (new UserBuilder())
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $this->categoryRepository
            ->expects($this->once())
            ->method('matching')
            ->willReturn([]);
        ($this->searchCategoriesByCriteria)($request);
    }

    public function testWhenRequestIsValidThenReturnCategories()
    {
        $request = new UnauthorizedSearchCategoriesByCriteriaRequest(
            [],
            null,
            null,
            null,
            null,
            null
        );
        $category = (new CategoryBuilder())->build();
        $anotherCategory = (new CategoryBuilder())->build();

        $expectedCategoryCollectionResponse = new CategoryCollectionResponse(
            ...
            $this->buildResponse(...[$category, $anotherCategory])
        );
        $this->categoryRepository
            ->expects($this->once())
            ->method('matching')
            ->willReturn([$category, $anotherCategory]);

        $actualCategoryCollectionResponse = ($this->searchCategoriesByCriteria)($request);
        $this->assertEquals($expectedCategoryCollectionResponse, $actualCategoryCollectionResponse);
    }

    private function buildResponse(Category ...$categories): array
    {
        return array_map(
            static function (Category $category) {
                return new CategoryResponse(
                    $category->getId()->toString(),
                    $category->getName(),
                    $category->getStatus()->value(),
                );
            },
            $categories
        );
    }

}
