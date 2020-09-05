<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class UpdateUnitRequest
{
    private string $requestAuthorId;
    private string $unitId;
    private string $name;
    private ?string $description;
    private string $level;
    private string $status;
    private string $courseId;

    public function __construct(
        string $requestAuthorId,
        string $courseId,
        string $unitId,
        string $name,
        ?string $description,
        string $level,
        string $status
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->level = $level;
        $this->status = $status;
        $this->courseId = $courseId;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getUnitId(): string
    {
        return $this->unitId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
