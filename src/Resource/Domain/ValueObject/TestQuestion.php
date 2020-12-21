<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\ValueObject;

use function Lambdish\Phunctional\map;

final class TestQuestion
{
    private string $question;
    private array $answers;

    public function __construct(string $question, TestAnswer ...$answers)
    {
        $this->question = $question;
        $this->answers = $answers;
    }

    public function toValues(): array
    {
        return [
            'question' => $this->question(),
            'answers' => map($this->answerToValues(), $this->answers()),
        ];
    }

    public static function fromValues(array $values): self
    {
        return new self($values['question'], ...map(self::valuesToAnswer(), $values['answers']));
    }

    public function question(): string
    {
        return $this->question;
    }

    public function answers(): array
    {
        return $this->answers;
    }

    private function answerToValues(): callable
    {
        return static function (TestAnswer $answer): array {
            return $answer->toValues();
        };
    }

    private static function valuesToAnswer(): callable
    {
        return static function (array $values): TestAnswer {
            return TestAnswer::fromValues($values);
        };
    }
}
