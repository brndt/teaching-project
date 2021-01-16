<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\CoursePermission\Application\Response;

use DateTimeImmutable;

final class CoursePermissionResponse
{
    public function __construct(
        private string $id,
        private string $courseId,
        private string $studentId,
        private DateTimeImmutable $created,
        private ?DateTimeImmutable $modified,
        private ?DateTimeImmutable $until,
        private string $status
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
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

    public function getStatus(): string
    {
        return $this->status;
    }
}
