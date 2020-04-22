<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain\Repository;

use LaSalle\StudentTeacher\Token\Domain\Aggregate\Token;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

interface TokenRepository
{
    public function create(User $user): Token;
}