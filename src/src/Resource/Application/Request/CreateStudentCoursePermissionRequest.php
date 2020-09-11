<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateStudentCoursePermissionRequest
{
    private string $requestAuthorId;
    private string $courseId;
    private string $studentId;
    private string $status;
    private ?\DateTimeImmutable $until;

    public function __construct(string $requestAuthorId, string $courseId, string $studentId, string $status, ?\DateTimeImmutable $until)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->courseId = $courseId;
        $this->studentId = $studentId;
        $this->status = $status;
        $this->until = $until;
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
