<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidStateException;

final class Approved implements State
{
    public const NAME = 'approved';

    public function tryTransition(State $newState, bool $isSpecifierChanged): void
    {
        if (true !== $newState instanceof Withdrawn) {
            throw new InvalidStateException("Can only withdraw");
        }
    }

    public function __toString()
    {
        return self::NAME;
    }
}