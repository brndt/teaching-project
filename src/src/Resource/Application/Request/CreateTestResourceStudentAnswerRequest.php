<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateTestResourceStudentAnswerRequest
{
    private string $requestAuthorId;
    private string $resourceId;
    private string $status;
    private array $assumptions;

    public function __construct(
        string $requestAuthorId,
        string $resourceId,
        string $status,
        array $assumptions
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->resourceId = $resourceId;
        $this->status = $status;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAssumptions(): array
    {
        return $this->assumptions;
    }
}
