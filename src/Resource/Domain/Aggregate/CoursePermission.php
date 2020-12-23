<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Aggregate;

use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class CoursePermission
{
    public function __construct(private Uuid $id, private Uuid $courseId, private Uuid $studentId, private \DateTimeImmutable $created, private ?\DateTimeImmutable $modified, private ?\DateTimeImmutable $until, private Status $status)
    {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCourseId(): Uuid
    {
        return $this->courseId;
    }

    public function setCourseId(Uuid $courseId): void
    {
        $this->courseId = $courseId;
    }

    public function getStudentId(): Uuid
    {
        return $this->studentId;
    }

    public function setStudentId(Uuid $studentId): void
    {
        $this->studentId = $studentId;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(\DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    public function getModified(): ?\DateTimeImmutable
    {
        return $this->modified;
    }

    public function setModified(?\DateTimeImmutable $modified): void
    {
        $this->modified = $modified;
    }

    public function getUntil(): ?\DateTimeImmutable
    {
        return $this->until;
    }

    public function setUntil(?\DateTimeImmutable $until): void
    {
        $this->until = $until;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }
}
