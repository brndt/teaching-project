<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

interface UserRepository
{
    public function save(User $user): void;

    public function updateBasicInformation(User $user): void;

    public function updatePassword(User $user): void;

    public function searchByEmail(string $email): ?User;

    public function searchByUuid(string $uuid): ?User;

    public function searchById(int $id): ?User;
}