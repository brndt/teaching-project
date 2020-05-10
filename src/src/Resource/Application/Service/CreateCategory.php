<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;

final class CreateCategory extends CategoryService
{
    public function __invoke(CreateCategoryRequest $request)
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());

        $requestAuthor = $this->userRepository->ofId($requestAuthorId);

        $this->ensureUserExists($requestAuthor);

        $this->ensureRequestAuthorCanExecute($requestAuthor);

        $this->ensureCategoryNotExistsWithThisName($request->getCategoryName());

        $category = new Category($this->categoryRepository->nextIdentity(), $request->getCategoryName());

        $this->categoryRepository->save($category);
    }
}