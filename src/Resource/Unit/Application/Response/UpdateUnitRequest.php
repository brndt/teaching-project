<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Unit\Application\Response;

final class UpdateUnitRequest
{
    public function __construct(
        private string $requestAuthorId,
        private string $courseId,
        private string $unitId,
        private string $name,
        private ?string $description,
        private string $level,
        private string $status
    ) {
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
