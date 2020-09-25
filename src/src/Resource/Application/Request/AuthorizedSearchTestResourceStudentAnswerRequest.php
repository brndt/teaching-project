<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class AuthorizedSearchTestResourceStudentAnswerRequest
{
    private string $requestAuthorId;
    private string $resourceId;
    private string $studentId;

    public function __construct(string $requestAuthorId, string $resourceId, string $studentId)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->resourceId = $resourceId;
        $this->studentId = $studentId;
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
