<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

final class TokensResponse
{
    public function __construct(private string $token, private string $refreshToken, private string $userId)
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
