<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

use LaSalle\StudentTeacher\User\Domain\Exception\InvalidStateException;
use Stringable;

final class Rejected implements State, Stringable
{
    public const NAME = 'rejected';

    public function ensureCanBeChanged(State $newState, bool $isSpecifierChanged): void
    {
        if (true !== ($newState instanceof Pended && false === $isSpecifierChanged)) {
            throw new InvalidStateException("Can only pending");
        }
    }

    public function __toString(): string
    {
        return self::NAME;
    }
}