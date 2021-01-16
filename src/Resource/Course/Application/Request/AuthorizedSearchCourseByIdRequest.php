<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Course\Application\Request;

final class AuthorizedSearchCourseByIdRequest
{
    public function __construct(private string $requestAuthorId, private string $courseId)
    {
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }
}
