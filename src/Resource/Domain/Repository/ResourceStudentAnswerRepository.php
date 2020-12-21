<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Repository;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\ResourceStudentAnswer;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

interface ResourceStudentAnswerRepository
{
    public function save(ResourceStudentAnswer $resourceStudentAnswer): void;

    public function ofId(Uuid $id): ?ResourceStudentAnswer;

    public function nextIdentity(): Uuid;

    public function matching(Criteria $criteria): array;
}
