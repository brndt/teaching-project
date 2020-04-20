<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\ValueObject;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid
{
    private string $id;

    public static function generate(): self
    {
        return new self((string) RamseyUuid::uuid4());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    private function __construct(string $id)
    {
        $this->guardIdIsValid($id);

        $this->id = $id;
    }

    private function guardIdIsValid(string $id): void
    {
        if (!RamseyUuid::isValid($id)) {
            throw new InvalidUuidException();
        }
    }

    public function equals($other): bool
    {
        return $this->id === $other->id;
    }

    public function getValue(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}