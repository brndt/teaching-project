<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\SearchCategoriesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CategoryCollectionResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;

final class SearchCategoriesByCriteria extends CategoryService
{
    public function __invoke(SearchCategoriesByCriteriaRequest $request): CategoryCollectionResponse
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);
        $this->ensureRequestAuthorIsAdmin($requestAuthor);

        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $categories = $this->categoryRepository->matching($criteria);

        $this->ensureCategoriesExist($categories);

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