<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain;

use DateTimeImmutable;

interface DomainEvent
{
    public function getId(): string;

    public function getOccurredOn(): DateTimeImmutable;
}