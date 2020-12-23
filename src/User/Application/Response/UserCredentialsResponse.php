<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

final class UserCredentialsResponse
{
    public function __construct(private string $id, private string $email, private string $password, private array $roles, private bool $enabled)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }
}