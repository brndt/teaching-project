<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\RefreshToken\Application\Request;

final class DeleteRefreshTokenRequest
{
    public function __construct(private $refreshTokenValue)
    {
    }

    public function getRefreshTokenValue(): string
    {
        return $this->refreshTokenValue;
    }
}