<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain;

use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

interface RandomStringGenerator
{
    public function generate(): string;
}