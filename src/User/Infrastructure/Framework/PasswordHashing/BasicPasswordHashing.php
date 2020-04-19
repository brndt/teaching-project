<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\PasswordHashing;

use LaSalle\StudentTeacher\User\Domain\PasswordHashing;

final class BasicPasswordHashing implements PasswordHashing
{

    public function verify(string $plainPassword, string $hash): bool
    {
        return password_verify($plainPassword, $hash);
    }

    public function hash_password(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }
}