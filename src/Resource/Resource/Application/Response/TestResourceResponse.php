<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Resource\Application\Response;

use DateTimeImmutable;

final class TestResourceResponse
{
    public function __construct(
        private string $id,
        private string $unitId,
        private string $name,
        private ?string $description,
        private DateTimeImmutable $created,
        private ?DateTimeImmutable $modified,
        private string $status,
        private string $content,
        private array $questions
    ) {
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }
}
