<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain;

interface DomainEventSubscriber
{
    public function handle(DomainEvent $aDomainEvent): void;
}