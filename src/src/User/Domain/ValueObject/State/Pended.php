<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidStateException;

final class Pended implements State
{
    public const NAME = 'pended';

    public function ensureCanBeChanged(State $newState, bool $isSpecifierChanged): void
    {
        if (
            true !== ($newState instanceof Withdrawn && false === $isSpecifierChanged) ||
            true !== $newState instanceof Approved && true === $isSpecifierChanged ||
            true !== ($newState instanceof Rejected) && true === $isSpecifierChanged
        ) {
            throw new InvalidStateException("Can only approve or reject if you react or withdraw if not");
        }
    }

    public function __toString()
    {
        return self::NAME;
    }
}