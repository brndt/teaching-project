<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

use DateTimeImmutable;

final class GenerateTokensRequest
{
    public function __construct(private string $userId, private DateTimeImmutable $expirationDate)
    {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getExpirationDate(): DateTimeImmutable
    {
        return $this->expirationDate;
    }
}