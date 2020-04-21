<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

interface UserRepository
{
    public function save(User $user): void;

    public function searchByEmail(Email $email): ?User;

    public function searchById(Uuid $id): ?User;
}