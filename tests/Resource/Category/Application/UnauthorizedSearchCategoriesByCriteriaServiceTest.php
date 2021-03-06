<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Category\Application;

use LaSalle\StudentTeacher\Resource\Category\Application\Request\UnauthorizedSearchCategoriesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Category\Application\Response\CategoryCollectionResponse;
use LaSalle\StudentTeacher\Resource\Category\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Category\Application\Service\UnauthorizedSearchCategoriesByCriteriaService;
use LaSalle\StudentTeacher\Resource\Category\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Category\Domain\Repository\CategoryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Category\Domain\CategoryBuilder;

final class UnauthorizedSearchCategoriesByCriteriaServiceTest extends TestCase
{
    private UnauthorizedSearchCategoriesByCriteriaService $searchCategoriesByCriteria;
    private MockObject $categoryRepository;

    public function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);

        $this->searchCategoriesByCriteria = new UnauthorizedSearchCategoriesByCriteriaService(
            $this->categoryRepository,
        );
    }

    public function testWhenCategoriesDontExistThenReturnEmptyArray()
    {
        $request = new UnauthorizedSearchCategoriesByCriteriaRequest(
            [],
            null,
            null,
            null,
            null,
            null
        );
        $expectedCategoryCollectionResponse = new CategoryCollectionResponse(...$this->buildResponse(...[]));
        $this->categoryRepository
            ->expects(self::once())
            ->method('matching')
            ->willReturn([]);
        $actualCategoryCollectionResponse = ($this->searchCategoriesByCriteria)($request);
        $this->assertEquals($expectedCategoryCollectionResponse, $actualCategoryCollectionResponse);
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
            ->expects(self::once())
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
