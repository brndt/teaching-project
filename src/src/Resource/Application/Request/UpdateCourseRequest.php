<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class UpdateCourseRequest
{
    private string $requestAuthorId;
    private string $id;
    private string $teacherId;
    private string $categoryId;
    private string $name;
    private ?string $description;
    private string $level;
    private ?\DateTimeImmutable $modified;
    private string $status;

    public function __construct(
        string $requestAuthorId,
        string $id,
        string $teacherId,
        string $categoryId,
        string $name,
        ?string $description,
        string $level,
        ?\DateTimeImmutable $modified,
        string $status
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->id = $id;
        $this->teacherId = $teacherId;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->description = $description;
        $this->level = $level;
        $this->modified = $modified;
        $this->status = $status;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
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

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function getModified(): ?\DateTimeImmutable
    {
        return $this->modified;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}