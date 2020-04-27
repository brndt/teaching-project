<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\ValueObject;

use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    private string $id;

    public static function  generate(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    /**
     * @throws InvalidUuidException
     */
    public function __construct(string $id)
    {
        self::assertUuidIsValid($id);
        $this->setUuid($id);
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    private function setUuid(string $id): void
    {
        $this->id = $id;
    }

    private static function assertUuidIsValid(string $id): void
    {
        if (false === RamseyUuid::isValid($id)) {
            throw new InvalidUuidException();
        }
    }
}