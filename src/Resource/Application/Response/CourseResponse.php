<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Response;

use DateTimeImmutable;

final class CourseResponse
{
    public function __construct(
        private string $id,
        private string $teacherId,
        private string $categoryId,
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

    public function getTeacherId(): string
    {
        return $this->teacherId;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
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