<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\ResourceType;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class Resource
{
    private Uuid $id;
    private Uuid $unitId;
    private string $name;
    private string $description;
    private string $content;
    private ResourceType $resourceType;
    private DateTimeImmutable $created;
    private DateTimeImmutable $modified;
    private Status $status;

    public function __construct(
        Uuid $id,
        Uuid $unitId,
        string $name,
        string $description,
        string $content,
        ResourceType $resourceType,
        DateTimeImmutable $created,
        DateTimeImmutable $modified,
        Status $status
    ) {
        $this->id = $id;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->content = $content;
        $this->resourceType = $resourceType;
        $this->created = $created;
        $this->modified = $modified;
        $this->status = $status;

    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getUnitId(): Uuid
    {
        return $this->unitId;
    }

    public function setUnitId(Uuid $unitId): void
    {
        $this->unitId = $unitId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }

    public function setResourceType(ResourceType $resourceType): void
    {
        $this->resourceType = $resourceType;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    public function getModified(): DateTimeImmutable
    {
        return $this->modified;
    }

    public function setModified(DateTimeImmutable $modified): void
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