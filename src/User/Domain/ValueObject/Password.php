<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidLetterContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidPasswordLengthException;

final class Password implements \Stringable
{
    private string $password;

    public static function fromHashedPassword(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    /**
     * @throws InvalidPasswordLengthException
     * @throws InvalidNumberContainingException
     * @throws InvalidLetterContainingException
     */
    public static function fromPlainPassword(string $plainPassword): self
    {
        self::assertMinimunLength($plainPassword);
        self::assertNumberContaining($plainPassword);
        self::assertLetterContaining($plainPassword);
        return new self(self::hash_password($plainPassword));
    }

    public function toString(): string
    {
        return $this->password;
    }

    public function __toString(): string
    {
        return $this->password;
    }

    public function verify(string $plainPassword): void
    {
        if (false === password_verify($plainPassword, $this->toString())) {
            throw new IncorrectPasswordException();
        }
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
        if (0 === preg_match('/[0-9]/', $password)) {
            throw new InvalidNumberContainingException();
        }
    }

    private static function assertLetterContaining(string $password): void
    {
        if (0 === preg_match('/[A-Za-z]/', $password)) {
            throw new InvalidLetterContainingException();
        }
    }

    private static function hash_password(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

}
