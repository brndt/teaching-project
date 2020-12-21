<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class AuthorizedSearchStudentCoursePermissionRequest
{
    private string $requestAuthorId;
    private string $courseId;
    private string $studentId;

    public function __construct(string $requestAuthorId, string $courseId, string $studentId)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->courseId = $courseId;
        $this->studentId = $studentId;
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
}
