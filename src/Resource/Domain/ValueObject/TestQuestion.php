<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\ValueObject;

use function Lambdish\Phunctional\map;

final class TestQuestion
{
    private array $answers;

    public function __construct(private string $question, TestAnswer ...$answers)
    {
        $this->answers = $answers;
    }

    public static function fromValues(array $values): self
    {
        return new self($values['question'], ...map(self::valuesToAnswer(), $values['answers']));
    }

    private static function valuesToAnswer(): callable
    {
        return static function (array $values): TestAnswer {
            return TestAnswer::fromValues($values);
        };
    }

    public function toValues(): array
    {
        return [
            'question' => $this->question(),
            'answers' => map($this->answerToValues(), $this->answers()),
        ];
    }

    public function question(): string
    {
        return $this->question;
    }

    private function answerToValues(): callable
    {
        return static function (TestAnswer $answer): array {
            return $answer->toValues();
        };
    }

    public function answers(): array
    {
        return $this->answers;
    }
}
