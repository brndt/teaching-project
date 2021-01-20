<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Domain\ValueObject;

use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidEmailException;
use Stringable;

final class Email implements Stringable
{
    private string $email;

    /**
     * @throws InvalidEmailException
     */
    public function __construct(string $email)
    {
        $this->setValue($email);
    }

    private function setValue(string $email)
    {
        $this->assertIsEmail($email);
        $this->email = $email;
    }

    private function assertIsEmail(string $email): void
    {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function equalsTo(self $email): bool
    {
        return $email->toString() === $this->toString();
    }

    public function toString(): string
    {
        return $this->email;
    }
}