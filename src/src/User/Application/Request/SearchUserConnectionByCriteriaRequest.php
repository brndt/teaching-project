<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class SearchUserConnectionByCriteriaRequest
{
    private string $requestAuthorId;
    private string $userId;
    private string $friendId;

    public function __construct(string $requestAuthorId, string $userId, string $friendId)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->userId = $userId;
        $this->friendId = $friendId;
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

}
