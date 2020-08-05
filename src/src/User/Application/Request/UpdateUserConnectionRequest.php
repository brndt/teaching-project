<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateUserConnectionRequest
{
    private string $requestAuthorId;
    private string $firstUser;
    private string $secondUser;
    private string $status;

    public function __construct(string $requestAuthorId, string $firstUser, string $secondUser, string $status)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->firstUser = $firstUser;
        $this->secondUser = $secondUser;
        $this->status = $status;
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

    public function getStatus(): string
    {
        return $this->status;
    }
}