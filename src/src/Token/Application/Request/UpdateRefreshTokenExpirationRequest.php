<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Request;

final class UpdateRefreshTokenExpirationRequest
{
    private \DateTime $newExpirationDate;
    private string $refreshToken;

    public function __construct(\DateTime $newExpirationDate, string $refreshToken)
    {
        $this->newExpirationDate = $newExpirationDate;
        $this->refreshToken = $refreshToken;
    }

    public function getNewExpirationDate(): \DateTime
    {
        return $this->newExpirationDate;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}