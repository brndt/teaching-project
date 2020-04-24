<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain\Repository;

use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

interface RefreshTokenRepository
{
    public function ofRefreshTokenString(RefreshTokenString $tokenValue): ?RefreshToken;

    public function delete(RefreshToken $token): void;

    public function save(RefreshToken $token): void;
}