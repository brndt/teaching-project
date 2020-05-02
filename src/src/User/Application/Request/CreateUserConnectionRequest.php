<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class CreateUserConnectionRequest
{
    private string $userId;
    private string $friendId;

    public function __construct(string $userId, string $friendId)
    {
        $this->userId = $userId;
        $this->friendId = $friendId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getFriendId(): string
    {
        return $this->friendId;
    }
}