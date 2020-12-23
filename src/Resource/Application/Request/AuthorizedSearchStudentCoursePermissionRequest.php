<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class AuthorizedSearchStudentCoursePermissionRequest
{
    public function __construct(private string $requestAuthorId, private string $courseId, private string $studentId)
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
}
