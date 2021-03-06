<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\RefreshToken\Application\Request;

use DateTimeImmutable;

final class UpdateRefreshTokenExpirationRequest
{
    public function __construct(private string $refreshToken, private DateTimeImmutable $newExpirationDate)
    {
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getNewExpirationDate(): DateTimeImmutable
    {
        return $this->newExpirationDate;
    }
}