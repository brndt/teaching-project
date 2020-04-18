<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Password\Update;

final class UpdateUserPasswordByIdRequest
{
    private int $id;
    private string $newPassword;

    public function __construct(int $id, string $newPassword)
    {
        $this->id = $id;
        $this->newPassword = $newPassword;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}