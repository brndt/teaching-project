<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateUserImageRequest
{
    public function __construct(private string $requestAuthorId, private string $userId, private string $image)
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

    public function getImage(): string
    {
        return $this->image;
    }
}
