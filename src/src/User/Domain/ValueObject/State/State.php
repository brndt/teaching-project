<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

interface State
{
    public function tryTransition(State $newState, bool $isSpecifierChanged): void;
    public function __toString();
}