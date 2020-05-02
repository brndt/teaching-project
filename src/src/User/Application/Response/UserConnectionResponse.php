<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

final class UserConnectionResponse
{
    private string $userId;
    private string $friendId;
    private string $status;

    public function __construct(string $userId, string $friendId, string $status)
    {
        $this->userId = $userId;
        $this->friendId = $friendId;
        $this->status = $status;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getFriendId(): string
    {
        return $this->friendId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}