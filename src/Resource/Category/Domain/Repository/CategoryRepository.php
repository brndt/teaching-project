<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Domain\Repository;

use LaSalle\StudentTeacher\Resource\Category\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

interface CategoryRepository
{
    public function save(Category $category): void;

    public function ofId(Uuid $id): ?Category;

    public function ofName(string $name): ?Category;

    public function nextIdentity(): Uuid;

    public function matching(Criteria $criteria): array;
}