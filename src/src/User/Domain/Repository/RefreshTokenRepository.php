<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Repository;

use LaSalle\StudentTeacher\User\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

interface RefreshTokenRepository
{
    public function ofToken(Token $tokenValue): ?RefreshToken;

    public function nextIdentity(): Token;

    public function delete(RefreshToken $token): void;

    public function save(RefreshToken $token): void;
}