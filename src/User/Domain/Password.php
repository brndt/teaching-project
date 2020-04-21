<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

use LaSalle\StudentTeacher\User\Domain\Exception\InvalidLetterContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidPasswordLengthException;

final class Password
{
    private string $password;

    public static function fromHashedPassword(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public static function fromPlainString(string $plainPassword): self
    {
        self::assertMinimunLength($plainPassword);
        self::assertNumberContaining($plainPassword);
        self::assertLetterContaining($plainPassword);
        return new self(self::hash_password($plainPassword));
    }

    public function toPrimitives(): string
    {
        return $this->password;
    }

    public function __toString()
    {
        return $this->password;
    }

    public static function verify(string $plainPassword, Password $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword->toPrimitives());
    }

    private function __construct(string $password)
    {
        $this->setValue($password);
    }

    private function setValue(string $password)
    {
        $this->password = $password;
    }

    private static function assertMinimunLength(string $password): void
    {
        if (8 > strlen($password)) {
            throw new InvalidPasswordLengthException();
        }
    }

    private static function assertNumberContaining(string $password): void
    {
        if (false === preg_match('/[0-9]/', $password)) {
            throw new InvalidNumberContainingException();
        }
    }

    private static function assertLetterContaining(string $password): void
    {
        if (false === preg_match('/[A-Za-z]/', $password)) {
            throw new InvalidLetterContainingException();
        }
    }

    private static function hash_password(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

}