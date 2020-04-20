<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Infrastructure\Persistence;

use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

final class InMemoryRefreshTokenRepository implements RefreshTokenRepository
{
    private array $refreshTokens = [];

    public function searchByTokenValue(string $tokenValue): ?RefreshToken
    {
        return $this->refreshTokens[$tokenValue] ?? null;
    }

    public function delete(RefreshToken $token): void
    {
        unset($this->refreshTokens[$token->getRefreshToken()]);
    }

    public function save(RefreshToken $token): void
    {
        $this->refreshTokens[$token->getRefreshToken()] = $token;
    }
}