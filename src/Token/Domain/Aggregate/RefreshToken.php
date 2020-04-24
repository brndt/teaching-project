<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain\Aggregate;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class RefreshToken
{
    private RefreshTokenString $refreshToken;
    private Uuid $userId;
    private \DateTime $expirationDate;

    public function __construct(RefreshTokenString $refreshToken, Uuid $userId, \DateTime $expirationDate)
    {
        $this->refreshToken = $refreshToken;
        $this->userId = $userId;
        $this->expirationDate = $expirationDate;
    }

    public function getRefreshToken(): RefreshTokenString
    {
        return $this->refreshToken;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

    private function setRefreshToken(RefreshTokenString $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function setUserId(Uuid $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setValid(\DateTime $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function isExpired()
    {
        return $this->expirationDate <= new \DateTime();
    }

}