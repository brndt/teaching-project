<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\UpdateCategoryRequest;

final class UpdateCategory extends CategoryService
{
    public function __invoke(UpdateCategoryRequest $request)
    {
        $requestAuthorId = $this->createIdFromPrimitive($request->getRequestAuthorId());

        $requestAuthor = $this->userRepository->ofId($requestAuthorId);

        $this->ensureRequestAuthorIsAdmin($requestAuthor);

        $categoryId = $this->createIdFromPrimitive($request->getCategoryId());

        $category = $this->categoryRepository->ofId($categoryId);

        $this->ensureCategoryExists($category);

        $this->ensureCategoryNotExistsWithThisName($request->getNewName());

        $newStatus = $this->createStatusFromPrimitive($request->getNewStatus());

        $category->setName($request->getNewName());
        $category->setStatus($newStatus);

        $this->categoryRepository->save($category);
    }
}