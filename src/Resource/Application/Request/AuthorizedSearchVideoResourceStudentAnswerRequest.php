<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class AuthorizedSearchVideoResourceStudentAnswerRequest
{
    public function __construct(private string $requestAuthorId, private string $resourceId, private string $studentId)
    {
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }
}
