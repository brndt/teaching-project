<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\Criteria;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Enum;

final class Operator extends Enum implements \Stringable
{
    public const AND  = 'AND';
    public const OR = 'OR';

    public static function fromValue(?string $operator): Operator
    {
        return (null === $operator) ? new self(Operator::AND): new self($operator);
    }

    public function __toString(): string
    {
        return parent::__toString();
    }

    protected function throwExceptionForInvalidValue($value)
    {
        throw new InvalidArgumentException($value);
    }
}