<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateVideoResourceStudentAnswerRequest
{
    private string $requestAuthorId;
    private string $resourceId;
    private string $studentAnswer;

    public function __construct(
        string $requestAuthorId,
        string $resourceId,
        string $studentAnswer
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->resourceId = $resourceId;
        $this->studentAnswer = $studentAnswer;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getStudentAnswer(): string
    {
        return $this->studentAnswer;
    }
}
