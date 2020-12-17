<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Response;

use DateTimeImmutable;

final class VideoResourceResponse extends ResourceResponse
{

    private string $videoUrl;
    private string $videoDescription;

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
        string $videoDescription
    ) {
        parent::__construct($id, $unitId, $name, $description, $content, $created, $modified, $status);
        $this->videoUrl = $videoUrl;
        $this->videoDescription = $videoDescription;
    }

    public function getVideoUrl(): string
    {
        return $this->videoUrl;
    }

    public function getVideoDescription(): string
    {
        return $this->videoDescription;
    }
}
