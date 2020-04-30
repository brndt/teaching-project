<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain\Repository;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;

interface RefreshTokenRepository
{
    public function ofToken(Token $tokenValue): ?RefreshToken;

    public function nextIdentity(): Token;

    public function delete(RefreshToken $token): void;

    public function save(RefreshToken $token): void;
}