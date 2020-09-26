<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateTestResourceStudentAnswerRequest
{
    private string $requestAuthorId;
    private string $resourceId;
    private array $assumptions;

    public function __construct(
        string $requestAuthorId,
        string $resourceId,
        array $assumptions
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->resourceId = $resourceId;
        $this->assumptions = $assumptions;
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
