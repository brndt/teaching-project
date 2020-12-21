<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

use DateTimeImmutable;

final class CreateVideoResourceRequest
{
    private string $requestAuthor;
    private string $unitId;
    private string $name;
    private ?string $description;
    private string $content;
    private string $status;
    private string $videoUrl;
    private string $videoDescription;

    public function __construct(
        string $requestAuthor,
        string $unitId,
        string $name,
        ?string $description,
        string $content,
        string $status,
        string $videoUrl,
        string $videoDescription
    ) {
        $this->requestAuthor = $requestAuthor;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->content = $content;
        $this->status = $status;
        $this->videoUrl = $videoUrl;
        $this->videoDescription = $videoDescription;
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

    public function getVideoUrl(): string
    {
        return $this->videoUrl;
    }

    public function getVideoDescription(): string
    {
        return $this->videoDescription;
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
