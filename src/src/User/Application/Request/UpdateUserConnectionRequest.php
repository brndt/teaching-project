<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateUserConnectionRequest
{
    private string $requestAuthorId;
    private string $id;
    private string $secondUserId;
    private string $status;

    public function __construct(string $requestAuthorId, string $firstUserId, string $secondUserId, string $status)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->firstUserId = $firstUserId;
        $this->secondUserId = $secondUserId;
        $this->status = $status;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getFirstUserId(): string
    {
        return $this->firstUserId;
    }

    public function getSecondUserId(): string
    {
        return $this->secondUserId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}