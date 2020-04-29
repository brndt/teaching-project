<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

final class ConfirmationToken
{
    private string $confirmationToken;

    public static function generate(): self
    {
        return new self(bin2hex(openssl_random_pseudo_bytes(64)));
    }

    public function __construct(string $confirmationToken)
    {
        $this->setValue($confirmationToken);
    }

    public function toString(): string
    {
        return $this->confirmationToken;
    }

    public function __toString(): string
    {
        return $this->confirmationToken;
    }

    private function setValue(string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }
}