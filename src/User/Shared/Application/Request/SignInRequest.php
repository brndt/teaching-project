<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Shared\Application\Request;

final class SignInRequest
{
    public function __construct(private string $email, private string $password)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}