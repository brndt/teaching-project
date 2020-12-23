<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateVideoResourceRequest
{
    public function __construct(
        private string $requestAuthor,
        private string $unitId,
        private string $name,
        private ?string $description,
        private string $content,
        private string $status,
        private string $videoUrl,
        private string $videoDescription
    ) {
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
