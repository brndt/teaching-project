<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class SearchCourseRequest
{
    private string $requestAuthorId;
    private string $courseId;

    public function __construct(string $requestAuthorId, string $courseId)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->courseId = $courseId;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }
}