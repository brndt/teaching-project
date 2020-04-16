<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User\Create;

final class CreateUserRequest
{
    private string $email;
    private string $uuid;
    private string $password;
    private string $firstName;
    private string $lastName;
    private array $roles;

    public function __construct(
        string $email,
        string $uuid,
        string $password,
        string $firstName,
        string $lastName,
        array $roles
    ) {
        $this->email = $email;
        $this->uuid = $uuid;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = $roles;
    }

    public function getUuid(): string
    {
        return $this->uuid;
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
}