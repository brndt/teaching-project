<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class ConfirmUserEmailRequest
{
    private string $userId;
    private string $confirmationToken;

    public function __construct(string $userId, string $confirmationToken)
    {
        $this->userId = $userId;
        $this->confirmationToken = $confirmationToken;
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