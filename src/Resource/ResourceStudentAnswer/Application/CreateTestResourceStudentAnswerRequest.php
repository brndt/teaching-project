<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Application;

final class CreateTestResourceStudentAnswerRequest
{
    public function __construct(private string $requestAuthorId, private string $resourceId, private array $assumptions)
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

    public function getAssumptions(): array
    {
        return $this->assumptions;
    }
}
