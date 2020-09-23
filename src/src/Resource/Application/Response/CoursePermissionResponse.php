<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Response;

final class CoursePermissionResponse
{
    private string $id;
    private string $courseId;
    private string $studentId;
    private \DateTimeImmutable $created;
    private ?\DateTimeImmutable $modified;
    private ?\DateTimeImmutable $until;
    private string $status;

    public function __construct(
        string $id,
        string $courseId,
        string $studentId,
        \DateTimeImmutable $created,
        ?\DateTimeImmutable $modified,
        ?\DateTimeImmutable $until,
        string $status
    ) {
        $this->id = $id;
        $this->courseId = $courseId;
        $this->studentId = $studentId;
        $this->created = $created;
        $this->modified = $modified;
        $this->until = $until;
        $this->status = $status;
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

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function getModified(): ?\DateTimeImmutable
    {
        return $this->modified;
    }

    public function getUntil(): ?\DateTimeImmutable
    {
        return $this->until;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
