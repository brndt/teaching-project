<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateTestRecourseStudentAnswerRequest
{
    private string $requestAuthorId;
    private string $recourseId;
    private string $status;
    private array $assumptions;

    public function __construct(
        string $requestAuthorId,
        string $recourseId,
        string $status,
        array $assumptions
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->recourseId = $recourseId;
        $this->status = $status;
        $this->assumptions = $assumptions;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getRecourseId(): string
    {
        return $this->recourseId;
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
