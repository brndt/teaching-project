<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateUserConnectionRequest
{
    private string $requestAuthorId;
    private string $userId;
    private string $friendId;
    private string $status;

    public function __construct(string $requestAuthorId, string $userId, string $friendId, string $status)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->userId = $userId;
        $this->friendId = $friendId;
        $this->status = $status;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
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