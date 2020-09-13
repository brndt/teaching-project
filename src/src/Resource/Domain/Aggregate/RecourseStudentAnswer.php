<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

abstract class RecourseStudentAnswer
{
    private Uuid $id;
    private Uuid $recourseId;
    private Uuid $studentId;
    private ?string $points;
    private ?string $teacher_comment;
    private DateTimeImmutable $created;
    private ?DateTimeImmutable $modified;
    private ?DateTimeImmutable $until;
    private Status $status;

    public function __construct(
        Uuid $id,
        Uuid $recourseId,
        Uuid $studentId,
        ?string $points,
        ?string $teacherComment,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        ?DateTimeImmutable $until,
        Status $status
    ) {
        $this->id = $id;
        $this->recourseId = $recourseId;
        $this->studentId = $studentId;
        $this->points = $points;
        $this->teacher_comment = $teacherComment;
        $this->created = $created;
        $this->modified = $modified;
        $this->until = $until;
        $this->status = $status;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRecourseId(): Uuid
    {
        return $this->recourseId;
    }

    public function getStudentId(): Uuid
    {
        return $this->studentId;
    }

    public function getPoints(): ?string
    {
        return $this->points;
    }

    public function getTeacherComment(): ?string
    {
        return $this->teacher_comment;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getModified(): ?DateTimeImmutable
    {
        return $this->modified;
    }

    public function getUntil(): ?DateTimeImmutable
    {
        return $this->until;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
