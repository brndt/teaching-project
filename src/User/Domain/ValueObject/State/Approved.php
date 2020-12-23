<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

use LaSalle\StudentTeacher\User\Domain\Exception\InvalidStateException;
use Stringable;

final class Approved implements State, Stringable
{
    public const NAME = 'approved';

    public function ensureCanBeChanged(State $newState, bool $isSpecifierChanged): void
    {
        if (true !== $newState instanceof Withdrawn) {
            throw new InvalidStateException("Can only withdraw");
        }
    }

    public function __toString(): string
    {
        return self::NAME;
    }
}