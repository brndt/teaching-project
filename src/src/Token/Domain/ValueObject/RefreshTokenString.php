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

    public function __construct(string $refreshTokenString)
    {
        $this->setValue($refreshTokenString);
    }

    public function toString(): string
    {
        return $this->refreshTokenString;
    }

    public function __toString(): string
    {
        return $this->refreshTokenString;
    }

    private function setValue(string $refreshTokenString): void
    {
        $this->refreshTokenString = $refreshTokenString;
    }
}