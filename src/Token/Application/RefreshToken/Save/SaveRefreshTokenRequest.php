<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Save;

final class SaveRefreshTokenRequest
{
    private string $uuid;
    private string $refreshToken;
    private \DateTime $valid;

    public function __construct(string $uuid, string $refreshToken, \DateTime $valid)
    {
        $this->uuid = $uuid;
        $this->refreshToken = $refreshToken;
        $this->valid = $valid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getValid(): \DateTime
    {
        return $this->valid;
    }

}