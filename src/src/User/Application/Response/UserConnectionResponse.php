<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

final class UserConnectionResponse
{
    private string $userId;
    private string $friendId;
    private string $status;
    private string $specifierId;

    public function __construct(string $userId, string $friendId, string $status, string $specifierId)
    {
        $this->userId = $userId;
        $this->friendId = $friendId;
        $this->status = $status;
        $this->specifierId = $specifierId;
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