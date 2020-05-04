<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidStateException;

final class Withdrawn implements State
{
    public const NAME = 'withdrawn';

    public function tryTransition(State $newState, bool $isSpecifierChanged): void
    {
        if (true !== $newState instanceof Pending) {
            throw new InvalidStateException("Can only pending");
        }
    }

    public function __toString()
    {
        return self::NAME;
    }
}