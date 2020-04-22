<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Request;

final class SaveRefreshTokenRequest
{
    private string $userId;
    private \DateTime $expirationDate;

    public function __construct(string $userId, \DateTime $expirationDate)
    {
        $this->userId = $userId;
        $this->expirationDate = $expirationDate;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

}