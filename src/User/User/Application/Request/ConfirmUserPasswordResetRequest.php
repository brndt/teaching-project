<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Application\Request;

final class ConfirmUserPasswordResetRequest
{
    public function __construct(private string $userId, private string $newPassword, private string $confirmationToken)
    {
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