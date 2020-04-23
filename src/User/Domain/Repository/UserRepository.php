<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Repository;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

interface UserRepository
{
    public function save(User $user): void;

    public function UserOfId(Uuid $id): ?User;

    public function matching(Criteria $criteria): array;
}