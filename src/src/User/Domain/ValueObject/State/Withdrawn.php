<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

use LaSalle\StudentTeacher\User\Domain\Exception\InvalidStateException;

final class Withdrawn implements State
{
    public const NAME = 'withdrawn';

    public function ensureCanBeChanged(State $newState, bool $isSpecifierChanged): void
    {
        if (true !== $newState instanceof Pended) {
            throw new InvalidStateException("Can only pending");
        }
    }

    public function __toString()
    {
        return self::NAME;
    }
}