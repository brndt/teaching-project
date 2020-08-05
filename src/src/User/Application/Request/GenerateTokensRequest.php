<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class GenerateTokensRequest
{
    private string $userId;
    private \DateTimeImmutable $expirationDate;

    public function __construct(string $userId, \DateTimeImmutable $expirationDate)
    {
        $this->userId = $userId;
        $this->expirationDate = $expirationDate;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getExpirationDate(): \DateTimeImmutable
    {
        return $this->expirationDate;
    }
}