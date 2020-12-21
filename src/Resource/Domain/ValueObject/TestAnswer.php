<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\ValueObject;

final class TestAnswer
{
    private string $answer;
    private bool $isCorrect;

    public function __construct(string $answer, bool $isCorrect)
    {
        $this->answer    = $answer;
        $this->isCorrect = $isCorrect;
    }

    public function toValues(): array
    {
        return [
            'answer'     => $this->answer(),
            'isCorrect' => $this->isCorrect(),
        ];
    }

    public static function fromValues(array $values): self
    {
        return new self($values['answer'], $values['isCorrect']);
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
