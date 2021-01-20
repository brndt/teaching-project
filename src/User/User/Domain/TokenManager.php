<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Domain;

use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;

interface TokenManager
{
    public function generate(User $user): string;
}