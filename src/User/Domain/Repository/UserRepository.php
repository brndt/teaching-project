<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Repository;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

interface UserRepository
{
    public function save(User $user): void;

    public function ofId(Uuid $id): ?User;

    public function ofEmail(Email $email): ?User;

    public function matching(Criteria $criteria): array;
}