<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Response;

final class RefreshTokenResponse
{
    private string $refreshToken;
    private string $userId;
    private \DateTime $expirationDate;

    public function __construct(string $refreshToken, string $userId, \DateTime $expirationDate)
    {
        $this->refreshToken = $refreshToken;
        $this->userId = $userId;
        $this->expirationDate = $expirationDate;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

}