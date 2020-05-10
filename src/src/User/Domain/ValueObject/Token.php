<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

final class Token
{
    private string $token;

    public static function generate(): self
    {
        return new self(bin2hex(openssl_random_pseudo_bytes(64)));
    }

    public function __construct(string $token)
    {
        $this->setValue($token);
    }

    public function toString(): string
    {
        return $this->token;
    }

    public function __toString(): string
    {
        return $this->token;
    }

    public function equalsTo(self $confirmationToken): bool
    {
        return $confirmationToken->toString() === $this->toString();
    }

    private function setValue(string $token): void
    {
        $this->token = $token;
    }
}