<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Application\Service;

use LaSalle\StudentTeacher\Resource\Category\Application\Request\UnauthorizedSearchCategoriesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Category\Application\Response\CategoryCollectionResponse;
use LaSalle\StudentTeacher\Resource\Category\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Category\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Category\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;

final class UnauthorizedSearchCategoriesByCriteriaService
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    public function __invoke(UnauthorizedSearchCategoriesByCriteriaRequest $request): CategoryCollectionResponse
    {
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $categories = $this->categoryRepository->matching($criteria);

        return new CategoryCollectionResponse(...$this->buildResponse(...$categories));
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
