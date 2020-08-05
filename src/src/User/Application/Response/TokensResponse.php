<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

final class TokensResponse
{
    private string $token;
    private string $refreshToken;
    private string $userId;

    public function __construct(string $token, string $refreshToken, string $userId)
    {
        $this->token = $token;
        $this->refreshToken = $refreshToken;
        $this->userId = $userId;
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
