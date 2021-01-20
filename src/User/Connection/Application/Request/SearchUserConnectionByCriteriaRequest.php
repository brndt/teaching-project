<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Connection\Application\Request;

final class SearchUserConnectionByCriteriaRequest
{
    public function __construct(private string $requestAuthorId, private string $userId, private string $friendId)
    {
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
