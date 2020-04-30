<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Request;

final class RefreshTokensRequest
{
    private string $refreshToken;
    private \DateTime $newExpirationDate;

    public function __construct(string $refreshToken, \DateTime $newExpirationDate)
    {
        $this->refreshToken = $refreshToken;
        $this->newExpirationDate = $newExpirationDate;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getNewExpirationDate(): \DateTime
    {
        return $this->newExpirationDate;
    }
}