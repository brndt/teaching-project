<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Delete;

final class DeleteRefreshTokenByTokenValueRequest
{
    private string $refreshTokenValue;

    public function __construct($refreshTokenValue)
    {
        $this->refreshTokenValue = $refreshTokenValue;
    }

    public function getRefreshTokenValue(): string
    {
        return $this->refreshTokenValue;
    }


}