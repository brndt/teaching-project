<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class ConfirmUserEmailRequest
{
    public function __construct(private string $userId, private string $confirmationToken)
    {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }
}