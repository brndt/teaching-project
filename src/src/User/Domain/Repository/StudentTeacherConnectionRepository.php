<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Repository;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\StudentTeacherConnection;

interface StudentTeacherConnectionRepository
{
    public function save(StudentTeacherConnection $user): void;

    public function ofId(Uuid $studentId, Uuid $teacherId): ?StudentTeacherConnection;

    public function nextIdentity(): Uuid;

    public function matching(Criteria $criteria): array;
}