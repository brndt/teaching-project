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
    private DateTimeImmutable $created;
    private ?DateTimeImmutable $modified;
    private string $status;
    private string $videoUrl;
    private string $text;

    public function __construct(
        string $requestAuthor,
        string $unitId,
        string $name,
        ?string $description,
        string $content,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        string $status,
        string $videoUrl,
        string $text
    ) {
        $this->requestAuthor = $requestAuthor;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->content = $content;
        $this->created = $created;
        $this->modified = $modified;
        $this->status = $status;
        $this->videoUrl = $videoUrl;
        $this->text = $text;
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

    public function getVideoUrl(): string
    {
        return $this->videoUrl;
    }

    public function getText(): string
    {
        return $this->text;
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
