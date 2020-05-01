<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class SendPasswordResetRequest
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}