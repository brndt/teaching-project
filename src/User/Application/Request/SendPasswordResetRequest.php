<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class SendPasswordResetRequest
{
    public function __construct(private string $email)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}