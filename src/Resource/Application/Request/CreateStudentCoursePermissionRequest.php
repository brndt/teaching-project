<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateStudentCoursePermissionRequest
{
    public function __construct(private string $requestAuthorId, private string $courseId, private string $studentId, private string $status, private ?\DateTimeImmutable $until)
    {
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUntil(): ?\DateTimeImmutable
    {
        return $this->until;
    }
}
