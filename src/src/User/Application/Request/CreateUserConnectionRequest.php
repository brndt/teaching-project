<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class CreateUserConnectionRequest
{
    private string $requestAuthorId;
    private string $firstUser;
    private string $secondUser;

    public function __construct(string $requestAuthorId, string $firstUser, string $secondUser)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->firstUser = $firstUser;
        $this->secondUser = $secondUser;
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