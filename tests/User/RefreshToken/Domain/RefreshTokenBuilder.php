<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\RefreshToken\Domain;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\RefreshToken\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;

final class RefreshTokenBuilder
{
    private Token $refreshToken;
    private Uuid $userId;
    private \DateTimeImmutable $expirationDate;

    public function __construct()
    {
        $this->refreshToken = new Token('some_refresh_token');
        $this->userId = Uuid::generate();
        $this->expirationDate = new DateTimeImmutable();
    }

    public function withRefreshToken(Token $refreshToken): RefreshTokenBuilder
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function withUserId(Uuid $userId): RefreshTokenBuilder
    {
        $this->userId = $userId;
        return $this;
    }

    public function withExpirationDate(DateTimeImmutable $expirationDate): RefreshTokenBuilder
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function build()
    {
        return new RefreshToken(
            $this->refreshToken,
            $this->userId,
            $this->expirationDate
        );
    }
}