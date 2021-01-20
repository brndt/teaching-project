<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Connection\Application\Request;

final class CreateUserConnectionRequest
{
    public function __construct(private string $requestAuthorId, private string $firstUser, private string $secondUser)
    {
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getFirstUser(): string
    {
        return $this->firstUser;
    }

    public function getSecondUser(): string
    {
        return $this->secondUser;
    }
}