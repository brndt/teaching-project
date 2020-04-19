<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

interface PasswordHashing
{
    public function verify(string $plainPassword, string $hash): bool;
    public function hash_password(string $plainPassword): string;
}