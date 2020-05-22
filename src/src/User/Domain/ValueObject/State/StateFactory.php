<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

class StateFactory
{
    public function create(string $state): State
    {
        return self::fromStateName(ucfirst($state));
    }

    public static function fromStateName(string $state): State
    {
        $newClass = 'LaSalle\StudentTeacher\User\Domain\ValueObject\State\\' . ucfirst($state);

        if (false === class_exists($newClass)) {
            throw new \InvalidArgumentException();
        }
        return new $newClass();
    }
}