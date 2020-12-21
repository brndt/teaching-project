<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateTestResourceRequest
{
    private string $requestAuthor;
    private string $unitId;
    private string $name;
    private ?string $description;
    private string $content;
    private string $status;
    private array $questions;

    public function __construct(
        string $requestAuthor,
        string $unitId,
        string $name,
        ?string $description,
        string $content,
        string $status,
        array $questions
    ) {
        $this->requestAuthor = $requestAuthor;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->content = $content;
        $this->status = $status;
        $this->questions = $questions;
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
