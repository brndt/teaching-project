<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateRefreshTokenExpirationRequest
{
    private string $refreshToken;
    private \DateTimeImmutable $newExpirationDate;

    public function __construct(string $refreshToken, \DateTimeImmutable $newExpirationDate)
    {
        $this->refreshToken = $refreshToken;
        $this->newExpirationDate = $newExpirationDate;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getNewExpirationDate(): \DateTimeImmutable
    {
        return $this->newExpirationDate;
    }
}