<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Course\Domain\Repository;

use LaSalle\StudentTeacher\Resource\Course\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

interface CourseRepository
{
    public function save(Course $category): void;

    public function ofId(Uuid $id): ?Course;

    public function nextIdentity(): Uuid;

    public function matching(Criteria $criteria): array;
}