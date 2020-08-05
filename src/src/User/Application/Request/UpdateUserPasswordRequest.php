<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateUserPasswordRequest
{
    private string $requestAuthorId;
    private string $userId;
    private string $oldPassword;
    private string $newPassword;

    public function __construct(string $requestAuthorId, string $userId, string $oldPassword, string $newPassword)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->userId = $userId;
        $this->oldPassword = $oldPassword;
        $this->newPassword = $newPassword;
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