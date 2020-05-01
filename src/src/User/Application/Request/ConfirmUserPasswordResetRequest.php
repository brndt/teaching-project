<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class ConfirmUserPasswordResetRequest
{
    private string $userId;
    private string $newPassword;
    private string $confirmationToken;

    public function __construct(string $userId, string $newPassword, string $confirmationToken)
    {
        $this->userId = $userId;
        $this->newPassword = $newPassword;
        $this->confirmationToken = $confirmationToken;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }
}