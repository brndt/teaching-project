<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\TestQuestion;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class TestResource extends Resource
{
    private array $questions;

    public function __construct(
        Uuid $id,
        Uuid $unitId,
        string $name,
        ?string $description,
        string $content,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        Status $status,
        TestQuestion ...$questions
    ) {
        parent::__construct($id, $unitId, $name, $description, $content, $created, $modified, $status);
        $this->questions = $questions;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }
}
