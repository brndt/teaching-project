<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain;

interface RandomStringGenerator
{
    public function generate(): string;
}