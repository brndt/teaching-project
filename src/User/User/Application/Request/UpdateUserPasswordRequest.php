<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Application\Request;

final class UpdateUserPasswordRequest
{
    public function __construct(
        private string $requestAuthorId,
        private string $userId,
        private string $oldPassword,
        private string $newPassword
    ) {
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}