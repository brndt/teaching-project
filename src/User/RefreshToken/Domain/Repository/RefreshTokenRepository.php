<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\RefreshToken\Domain\Repository;

use LaSalle\StudentTeacher\User\RefreshToken\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;

interface RefreshTokenRepository
{
    public function ofToken(Token $tokenValue): ?RefreshToken;

    public function delete(RefreshToken $token): void;

    public function save(RefreshToken $token): void;
}