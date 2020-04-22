<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateUserPasswordRequest
{
    private string $id;
    private string $oldPassword;
    private string $newPassword;

    public function __construct(string $id, string $oldPassword, string $newPassword)
    {
        $this->id = $id;
        $this->oldPassword = $oldPassword;
        $this->newPassword = $newPassword;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }
}