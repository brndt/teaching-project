<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\ValueObject;

use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Stringable;

class Uuid implements Stringable
{
    private string $id;

    /**
     * @throws InvalidUuidException
     */
    public function __construct(string $id)
    {
        self::assertUuidIsValid($id);
        $this->setUuid($id);
    }

    private static function assertUuidIsValid(string $id): void
    {
        if (false === RamseyUuid::isValid($id)) {
            throw new InvalidUuidException();
        }
    }

    private function setUuid(string $id): void
    {
        $this->id = $id;
    }

    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equalsTo(self $uuid): bool
    {
        return $uuid->toString() === $this->toString();
    }

    public function toString(): string
    {
        return $this->id;
    }
}