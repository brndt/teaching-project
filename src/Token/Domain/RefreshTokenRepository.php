<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain;

interface RefreshTokenRepository
{
    public function searchByTokenValue(string $tokenValue): ?RefreshToken;
    public function delete(RefreshToken $token): void;
    public function save(RefreshToken $token): void;
}