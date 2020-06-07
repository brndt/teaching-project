<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCategoryByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;

final class AuthorizedSearchCategoryByIdService extends CategoryService
{
    public function __invoke(AuthorizedSearchCategoryByIdRequest $request): CategoryResponse
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $requestAuthor = $this->userRepository->ofId($requestAuthorId);
        $this->ensureUserExists($requestAuthor);
        $this->ensureRequestAuthorIsAdmin($requestAuthor);

        $category = $this->categoryRepository->ofId($this->createIdFromPrimitive($request->getCategoryId()));

        $this->ensureCategoryExists($category);

        return $this->buildResponse($category);
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
