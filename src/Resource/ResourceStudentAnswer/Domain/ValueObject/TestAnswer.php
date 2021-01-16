<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\ResourceStudentAnswer\Domain\ValueObject;

final class TestAnswer
{
    public function __construct(private string $answer, private bool $isCorrect)
    {
    }

    public static function fromValues(array $values): self
    {
        return new self($values['answer'], $values['isCorrect']);
    }

    public function toValues(): array
    {
        return [
            'answer' => $this->answer(),
            'isCorrect' => $this->isCorrect(),
        ];
    }

    public function answer(): string
    {
        return $this->answer;
    }

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }
}
