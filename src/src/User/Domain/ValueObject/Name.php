<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNameException;

final class Name
{
    private string $name;

    /**
     * @throws InvalidNameException
     */
    public function __construct(string $name)
    {
        $this->setValue($name);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    private function setValue(string $name)
    {
        $this->assertIsValid($name);
        $this->name = $name;
    }

    private function assertIsValid(string $name): void
    {
        if (0 === preg_match("/^[a-zA-Z'-]+$/", $name)) {
            throw new InvalidNameException();
        }
    }

}