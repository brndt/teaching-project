<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Aggregate;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class RefreshToken
{
    private Token $refreshToken;
    private Uuid $userId;
    private \DateTimeImmutable $expirationDate;

    public function __construct(Token $refreshToken, Uuid $userId, \DateTimeImmutable $expirationDate)
    {
        $this->refreshToken = $refreshToken;
        $this->userId = $userId;
        $this->expirationDate = $expirationDate;
    }

    public function getRefreshToken(): Token
    {
        return $this->refreshToken;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getExpirationDate(): \DateTimeImmutable
    {
        return $this->expirationDate;
    }

    private function setRefreshToken(Token $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function setUserId(Uuid $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setValid(\DateTimeImmutable $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function isExpired()
    {
        return $this->expirationDate <= new \DateTimeImmutable();
    }

}