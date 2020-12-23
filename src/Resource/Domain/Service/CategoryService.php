<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Service;

use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryAlreadyExists;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class CategoryService
{
    public function __construct(private CategoryRepository $repository)
    {
    }

    public function findCategory(Uuid $id): Category
    {
        $course = $this->repository->ofId($id);
        if (null === $course) {
            throw new CategoryNotFound();
        }
        return $course;
    }

    public function ensureCategoryNotExistsWithThisName(string $categoryName): void
    {
        $category = $this->repository->ofName($categoryName);
        if (null !== $category) {
            throw new CategoryAlreadyExists();
        }
    }

    public function ensureCategoryNameIsAvailable(string $oldCategoryName, string $newCategoryName): void
    {
        $category = $this->repository->ofName($newCategoryName);
        if (null !== $category && $oldCategoryName !== $category->getName()) {
            throw new CategoryAlreadyExists();
        }
    }
}
