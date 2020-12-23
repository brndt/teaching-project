<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\ValueObject;

use function Lambdish\Phunctional\map;

final class StudentTestAnswer
{
    private array $answers;

    public function __construct(private string $question, private string $studentAssumption, TestAnswer ...$answers)
    {
        $this->answers = $answers;
    }

    public static function fromValues(array $values): self
    {
        return new self(
            $values['question'],
            $values['student_assumption'],
            ...
            map(self::valuesToAnswer(), $values['answers'])
        );
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
            'student_assumption' => $this->studentAssumption(),
            'answers' => map($this->answerToValues(), $this->answers()),
        ];
    }

    public function question(): string
    {
        return $this->question;
    }

    public function studentAssumption(): string
    {
        return $this->studentAssumption;
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
