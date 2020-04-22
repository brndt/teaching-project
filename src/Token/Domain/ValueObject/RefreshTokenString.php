<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain\ValueObject;

final class RefreshTokenString
{
    private string $refreshTokenString;

    public static function generate(): self
    {
        return new self(bin2hex(openssl_random_pseudo_bytes(64)));
    }

    public static function fromString(string $refreshTokenString): self
    {
        return new self($refreshTokenString);
    }

    public function toString(): string
    {
        return $this->refreshTokenString;
    }

    public function __toString(): string
    {
        return $this->refreshTokenString;
    }

    private function __construct(string $refreshTokenString)
    {
        $this->setValue($refreshTokenString);
    }

    private function setValue(string $refreshTokenString): void
    {
        $this->refreshTokenString = $refreshTokenString;
    }
}