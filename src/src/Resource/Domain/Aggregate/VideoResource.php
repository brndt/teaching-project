<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class VideoResource extends Resource
{
    private string $videoUrl;
    private string $text;

    public function __construct(
        Uuid $id,
        Uuid $unitId,
        string $name,
        ?string $description,
        string $content,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        Status $status,
        string $videoUrl,
        string $text
    ) {
        parent::__construct($id, $unitId, $name, $description, $content, $created, $modified, $status);

        $this->videoUrl = $videoUrl;
        $this->text = $text;
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
