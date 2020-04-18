<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken;

final class RefreshTokenResponse
{
    private ?int $id;
    private string $refreshToken;
    private string $uuid;
    private \DateTime $valid;

    public function __construct(string $uuid, string $refreshToken, \DateTime $valid, int $id = null)
    {
        $this->id = $id;
        $this->refreshToken = $refreshToken;
        $this->uuid = $uuid;
        $this->valid = $valid;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getValid(): \DateTime
    {
        return $this->valid;
    }

}