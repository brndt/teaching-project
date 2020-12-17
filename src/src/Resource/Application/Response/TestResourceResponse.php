<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Response;

use DateTimeImmutable;

final class TestResourceResponse extends ResourceResponse
{
    private array $questions;

    public function __construct(
        string $id,
        string $unitId,
        string $name,
        ?string $description,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        string $status,
        string $content,
        array $questions
    ) {
        parent::__construct($id, $unitId, $name, $description, $content, $created, $modified, $status);
        $this->questions = $questions;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }
}
