<?php
declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateResourceRequest
{

    private string $requestAuthorId;
    private string $unitId;
    private string $name;
    private ?string $description;
    private string $content;
    private string $resourceType;
    private \DateTimeImmutable $created;
    private ?\DateTimeImmutable $modified;
    private string $status;

    public function __construct(
        string $requestAuthorId,
        string $unitId,
        string $name,
        ?string $description,
        string $content,
        string $resourceType,
        \DateTimeImmutable $created,
        ?\DateTimeImmutable $modified,
        string $status
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->content = $content;
        $this->resourceType = $resourceType;
        $this->created = $created;
        $this->modified = $modified;
        $this->status = $status;
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
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