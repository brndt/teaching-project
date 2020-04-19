<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain;

interface RefreshTokenGenerating
{
    public function __invoke(): string;
}