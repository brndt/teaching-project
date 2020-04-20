<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class RefreshToken
{
    private Uuid $id;
    private string $refreshToken;
    private Uuid $userId;
    private \DateTime $expirationDate;

    public function __construct(Uuid $id, string $refreshToken, Uuid $userId, \DateTime $expirationDate)
    {
        $this->id = $id;
        $this->refreshToken = $refreshToken;
        $this->userId = $userId;
        $this->expirationDate = $expirationDate;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRefreshToken(): string
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

    private function setRefreshToken($refreshToken)
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