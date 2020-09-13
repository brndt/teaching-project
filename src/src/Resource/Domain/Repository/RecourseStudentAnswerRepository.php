<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Repository;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\RecourseStudentAnswer;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

interface RecourseStudentAnswerRepository
{
    public function save(RecourseStudentAnswer $recourseStudentAnswer): void;

    public function ofId(Uuid $id): ?RecourseStudentAnswer;

    public function nextIdentity(): Uuid;

    public function matching(Criteria $criteria): array;
}
