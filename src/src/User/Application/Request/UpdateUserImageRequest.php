<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateUserImageRequest
{
    private string $userId;
    private string $image;
    private string $requestAuthorId;

    public function __construct(string $requestAuthorId, string $userId, string $image)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->userId = $userId;
        $this->image = $image;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getImage(): string
    {
        return $this->image;
    }
}
