<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\ValueObject;

use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid
{
    private string $id;

    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    private function __construct(string $id)
    {
        $this->setUuid($id);
    }

    private function setUuid(string $id): void
    {
        $this->assertUuidIsValid($id);
        $this->id = $id;
    }

    private function assertUuidIsValid(string $id): void
    {
        if (false === RamseyUuid::isValid($id)) {
            throw new InvalidUuidException();
        }
    }
}