<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

interface UserRepository
{
    public function save(User $user): void;
    public function update(User $user): void;
    public function searchByEmail(string $email): ?User;
}