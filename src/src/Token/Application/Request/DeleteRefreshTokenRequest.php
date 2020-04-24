<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Request;

final class DeleteRefreshTokenRequest
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