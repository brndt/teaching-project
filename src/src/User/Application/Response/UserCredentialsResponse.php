<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

final class UserCredentialsResponse
{
    private string $id;
    private string $email;
    private string $password;
    private array $roles;

    public function __construct(string $id, string $email, string $password, array $roles)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
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
}