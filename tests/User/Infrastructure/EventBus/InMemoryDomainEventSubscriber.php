<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Infrastructure\EventBus;

use LaSalle\StudentTeacher\Shared\Domain\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\DomainEventSubscriber;

final class InMemoryDomainEventSubscriber implements DomainEventSubscriber
{
    public DomainEvent $event;

    public function handle(DomainEvent $aDomainEvent): void
    {
        $this->event = $aDomainEvent;
    }

    public function isSubscribedTo(DomainEvent $aDomainEvent): bool
    {
        if ($aDomainEvent === $this->event) {
            return true;
        }
        return false;
    }
}