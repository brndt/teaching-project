<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Repository;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

interface UserRepository
{
    public function save(User $user): void;

    public function searchByEmail(Email $email): ?User;

    public function searchById(Uuid $id): ?User;
}