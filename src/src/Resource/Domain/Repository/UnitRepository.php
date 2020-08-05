<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Repository;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

interface UnitRepository
{
    public function save(Unit $category): void;

    public function ofId(Uuid $id): ?Unit;

    public function nextIdentity(): Uuid;

    public function matching(Criteria $criteria): array;
}