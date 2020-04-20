<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Token\Create;

final class CreateTokenRequest
{
    private string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}