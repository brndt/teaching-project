<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Update;

final class UpdateRefreshTokenValidationDateByTokenValueRequest
{
    private \DateTime $newValidation;
    private string $refreshToken;

    public function __construct(\DateTime $newValidation, string $refreshToken)
    {
        $this->newValidation = $newValidation;
        $this->refreshToken = $refreshToken;
    }

    public function getNewValidation(): \DateTime
    {
        return $this->newValidation;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }


}