<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject\State;

final class StateFactory
{
    public function create(string $state): State
    {
        return self::fromStateName(ucfirst($state));
    }

    public static function fromStateName(string $state): State
    {
        $newClass = __NAMESPACE__ . '\\' . ucfirst($state);

        if (false === class_exists($newClass)) {
            throw new \InvalidArgumentException();
        }
        return new $newClass();
    }
}