<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Aggregate;

use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class Course
{
    public function __construct(private Uuid $id, private Uuid $teacherId, private Uuid $categoryId, private string $name, private ?string $description, private string $level, private \DateTimeImmutable $created, private ?\DateTimeImmutable $modified, private Status $status)
    {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getTeacherId(): Uuid
    {
        return $this->teacherId;
    }

    public function setTeacherId(Uuid $teacherId): void
    {
        $this->teacherId = $teacherId;
    }

    public function getCategoryId(): Uuid
    {
        return $this->categoryId;
    }

    public function setCategoryId(Uuid $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(\DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    public function getModified(): ?\DateTimeImmutable
    {
        return $this->modified;
    }

    public function setModified(?\DateTimeImmutable $modified): void
    {
        $this->modified = $modified;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }
}