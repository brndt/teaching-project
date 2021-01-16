<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Unit\Application;

use DateTimeImmutable;

final class UnitResponse
{
    public function __construct(
        private string $id,
        private string $courseId,
        private string $name,
        private ?string $description,
        private string $level,
        private DateTimeImmutable $created,
        private ?DateTimeImmutable $modified,
        private string $status
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
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

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getModified(): ?DateTimeImmutable
    {
        return $this->modified;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
