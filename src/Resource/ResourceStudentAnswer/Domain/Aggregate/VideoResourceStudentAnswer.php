<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class VideoResourceStudentAnswer extends ResourceStudentAnswer
{
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
        private string $studentAnswer
    ) {
        parent::__construct($id, $resourceId, $studentId, $points, $teacherComment, $created, $modified, $until, $status);
    }

    public function getStudentAnswer(): string
    {
        return $this->studentAnswer;
    }
}
