<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

abstract class ResourceStudentAnswer
{
    public function __construct(
        private Uuid $id,
        private Uuid $resourceId,
        private Uuid $studentId,
        private ?string $points,
        private ?string $teacher_comment,
        private DateTimeImmutable $created,
        private ?DateTimeImmutable $modified,
        private ?DateTimeImmutable $until,
        private Status $status
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getResourceId(): Uuid
    {
        return $this->resourceId;
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
