<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain;

use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

interface TokenManager
{
    public function generate(User $user): string;
}