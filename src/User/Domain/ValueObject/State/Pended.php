<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

use LaSalle\StudentTeacher\User\Domain\Exception\InvalidStateException;

final class Pended implements State, \Stringable
{
    public const NAME = 'pended';

    public function ensureCanBeChanged(State $newState, bool $isSpecifierChanged): void
    {
        if (false === $this->verifyIfCanBeChanged($newState, $isSpecifierChanged)) {
            throw new InvalidStateException("Can only approve or reject if you react or withdraw if not");
        }
    }

    private function verifyIfCanBeChanged(State $newState, bool $isSpecifierChanged): bool
    {
        if ($newState instanceof Withdrawn && false === $isSpecifierChanged) {
            return true;
        }
        if ($newState instanceof Approved && true === $isSpecifierChanged) {
            return true;
        }
        if ($newState instanceof Rejected && true === $isSpecifierChanged) {
            return true;
        }
        return false;
    }

    public function __toString(): string
    {
        return self::NAME;
    }
}