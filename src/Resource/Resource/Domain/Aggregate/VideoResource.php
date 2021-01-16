<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Resource\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class VideoResource extends Resource
{
    public function __construct(
        Uuid $id,
        Uuid $unitId,
        string $name,
        ?string $description,
        string $content,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        Status $status,
        private string $videoUrl,
        private string $videoDescription
    ) {
        parent::__construct($id, $unitId, $name, $description, $content, $created, $modified, $status);
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
