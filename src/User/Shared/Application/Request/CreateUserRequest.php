<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Shared\Application\Request;

use DateTimeImmutable;

final class CreateUserRequest
{
    public function __construct(
        private string $email,
        private string $password,
        private string $firstName,
        private string $lastName,
        private array $roles,
        private DateTimeImmutable $created
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }
}