<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

interface DomainEvent
{
    public function getId(): Uuid;

    public function getOccurredOn(): DateTimeImmutable;
}