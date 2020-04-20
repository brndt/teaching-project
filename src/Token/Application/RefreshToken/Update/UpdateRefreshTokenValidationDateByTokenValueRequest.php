<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Update;

final class UpdateRefreshTokenValidationDateByTokenValueRequest
{
    private \DateTime $newValidationDate;
    private string $refreshToken;

    public function __construct(\DateTime $newValidationDate, string $refreshToken)
    {
        $this->newValidationDate = $newValidationDate;
        $this->refreshToken = $refreshToken;
    }

    public function getNewValidationDate(): \DateTime
    {
        return $this->newValidationDate;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}