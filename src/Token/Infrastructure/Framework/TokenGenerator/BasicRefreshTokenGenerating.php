<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Infrastructure\Framework\TokenGenerator;

use LaSalle\StudentTeacher\Token\Domain\RefreshTokenGenerating;

final class BasicRefreshTokenGenerating implements RefreshTokenGenerating
{
    public function __invoke(): string
    {
        return bin2hex(openssl_random_pseudo_bytes(64));
    }
}