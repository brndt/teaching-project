<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\ValueObject\State;

use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidStateException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\State\State;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\State\Withdrawn;
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