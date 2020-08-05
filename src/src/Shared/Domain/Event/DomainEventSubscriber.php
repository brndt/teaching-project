<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\Event;

interface DomainEventSubscriber
{
    public static function subscribedTo(): array;
}
