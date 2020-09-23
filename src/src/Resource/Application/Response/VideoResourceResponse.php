<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Response;

use DateTimeImmutable;

final class VideoResourceResponse
{
    private string $id;
    private string $unitId;
    private string $name;
    private ?string $description;
    private DateTimeImmutable $created;
    private ?DateTimeImmutable $modified;
    private string $status;
    private string $content;
    private string $videoUrl;
    private string $text;

    public function __construct(
        string $id,
        string $unitId,
        string $name,
        ?string $description,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        string $status,
        string $content,
        string $videoUrl,
        string $text
    ) {
        $this->id = $id;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->created = $created;
        $this->modified = $modified;
        $this->status = $status;
        $this->content = $content;
        $this->videoUrl = $videoUrl;
        $this->text = $text;
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

    public function getVideoUrl(): string
    {
        return $this->videoUrl;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
