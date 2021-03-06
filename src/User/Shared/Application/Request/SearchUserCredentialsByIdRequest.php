<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Shared\Application\Request;

final class SearchUserCredentialsByIdRequest
{
    public function __construct(private string $userId)
    {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}