<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateTestResourceRequest
{
    public function __construct(private string $requestAuthor, private string $unitId, private string $name, private ?string $description, private string $content, private string $status, private array $questions)
    {
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function getRequestAuthor(): string
    {
        return $this->requestAuthor;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
