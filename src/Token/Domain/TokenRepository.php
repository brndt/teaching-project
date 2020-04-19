<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain;

use LaSalle\StudentTeacher\User\Domain\User;

interface TokenRepository
{
    public function create(User $user): Token;
}