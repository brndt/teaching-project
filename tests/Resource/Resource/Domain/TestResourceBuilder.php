<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Resource\Domain;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Resource\Domain\Aggregate\TestResource;
use LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\ValueObject\TestQuestion;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class TestResourceBuilder
{
    private Uuid $id;
    private Uuid $unitId;
    private string $name;
    private ?string $description;
    private DateTimeImmutable $created;
    private ?DateTimeImmutable $modified;
    private Status $status;
    private string $content;
    private array $questions;

    public function __construct()
    {
        $this->id = Uuid::generate();
        $this->unitId = Uuid::generate();
        $this->name = 'some name';
        $this->description = 'some description';
        $this->created = new DateTimeImmutable();
        $this->modified = null;
        $this->status = new Status(Status::randomValue());
        $this->content = 'some content';
        $this->questions = array_map($this->questionMaker(), [
            "0" => [
                "question" => "hola como estas",
                "answers" => [
                    "0" => [
                        "answer" => "bien",
                        "isCorrect" => true
                    ],
                    "1" => [
                        "answer" => "mal",
                        "isCorrect" => false
                    ]
                ]
            ],
            "1" => [
                "question" => "hola que tal",
                "answers" => [
                    "0" => [
                        "answer" => "perfecto",
                        "isCorrect" => true
                    ],
                    "1" => [
                        "answer" => "fatal",
                        "isCorrect" => false
                    ]
                ]
            ]
        ]);
    }

    public function withId(Uuid $id): TestResourceBuilder
    {
        $this->id = $id;
        return $this;
    }

    public function withUnitId(Uuid $unitId): TestResourceBuilder
    {
        $this->unitId = $unitId;
        return $this;
    }

    public function withName(string $name): TestResourceBuilder
    {
        $this->name = $name;
        return $this;
    }

    public function withDescription(?string $description): TestResourceBuilder
    {
        $this->description = $description;
        return $this;
    }

    public function withCreated(DateTimeImmutable $created): TestResourceBuilder
    {
        $this->created = $created;
        return $this;
    }

    public function withModified(?DateTimeImmutable $modified): TestResourceBuilder
    {
        $this->modified = $modified;
        return $this;
    }

    public function withStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function withContent(string $content): TestResourceBuilder
    {
        $this->content = $content;
        return $this;
    }

    public function withQuestions(TestQuestion ...$questions): TestResourceBuilder
    {
        $this->questions = $questions;
        return $this;
    }

    public function build(): TestResource
    {
        return new TestResource(
            $this->id,
            $this->unitId,
            $this->name,
            $this->description,
            $this->content,
            $this->created,
            $this->modified,
            $this->status,
            ...$this->questions,
        );
    }

    private function questionMaker(): callable
    {
        return static function (array $values): TestQuestion {
            return TestQuestion::fromValues($values);
        };
    }
}
