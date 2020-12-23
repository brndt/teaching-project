<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class SearchUserByIdRequest
{
    public function __construct(private string $userId)
    {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
