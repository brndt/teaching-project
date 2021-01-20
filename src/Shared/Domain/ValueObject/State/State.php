<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\ValueObject\State;

interface State
{
    public function ensureCanBeChanged(State $newState, bool $isSpecifierChanged): void;

    public function __toString();
}