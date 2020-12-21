<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\StudentTestAnswer;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class TestResourceStudentAnswer extends ResourceStudentAnswer
{
    private array $assumptions;

    public function __construct(
        Uuid $id,
        Uuid $resourceId,
        Uuid $studentId,
        ?string $points,
        ?string $teacherComment,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        ?DateTimeImmutable $until,
        Status $status,
        StudentTestAnswer... $assumptions
    ) {
        parent::__construct($id, $resourceId, $studentId, $points, $teacherComment, $created, $modified, $until, $status);
        $this->assumptions = $assumptions;
    }

    public function getAssumptions(): array
    {
        return $this->assumptions;
    }
}
